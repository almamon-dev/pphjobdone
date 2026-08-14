import React, { useState } from "react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Head, router } from "@inertiajs/react";
import {
    Search,
    Trash2,
    Users as UsersIcon,
    ChevronDown,
    ChevronLeft,
    ChevronRight,
    User as UserIcon,
    Shield,
    Phone,
    Mail,
    Calendar,
    ShoppingBag,
    CheckCircle2,
    XCircle,
} from "lucide-react";

export default function Index({ users, filters = {}, auth }) {
    const [search, setSearch] = useState(filters.search || "");

    const handleSearch = (value) => {
        setSearch(value);
        updateFilters({ search: value, page: 1 });
    };

    const updateFilters = (newFilters) => {
        router.get(
            route("admin.users.index"),
            { ...filters, ...newFilters },
            { preserveState: true, replace: true }
        );
    };

    const handlePerPageChange = (e) => {
        updateFilters({ per_page: e.target.value, page: 1 });
    };

    const handlePageChange = (url) => {
        if (url) router.get(url, {}, { preserveState: true });
    };

    const handleDelete = (id) => {
        if (confirm("Are you sure you want to delete this user?")) {
            router.delete(route("admin.users.destroy", id));
        }
    };

    return (
        <AdminLayout>
            <Head title="User Management" />

            <div className="space-y-4 max-w-[1600px] mx-auto pb-12">
                {/* TOP HEADER */}
                <div className="bg-white rounded-md p-5 border border-slate-200/80 shadow-2xs flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-md bg-[#0a66c2]/10 text-[#0a66c2] flex items-center justify-center border border-[#0a66c2]/20">
                            <UsersIcon size={22} />
                        </div>
                        <div>
                            <h1 className="text-xl font-bold text-slate-900 leading-tight">
                                User Management
                            </h1>
                            <p className="text-sm text-slate-600 mt-0.5">
                                View, filter, and manage registered clients, administrators, and booking activity.
                            </p>
                        </div>
                    </div>

                    <div className="text-sm font-semibold text-slate-700 bg-slate-100 px-3.5 py-1.5 rounded-md border border-slate-200">
                        Total Users: <strong className="text-slate-900">{users.total || 0}</strong>
                    </div>
                </div>

                {/* MAIN TABLE CONTAINER */}
                <div className="bg-white rounded-md border border-slate-200/80 shadow-2xs overflow-hidden">
                    {/* SEARCH & FILTER BAR */}
                    <div className="p-4 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div className="relative w-full sm:w-[380px]">
                            <div className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                                <Search size={17} />
                            </div>
                            <input
                                type="text"
                                value={search}
                                onChange={(e) => handleSearch(e.target.value)}
                                placeholder="Search by name, email, or phone..."
                                className="w-full h-10 pl-10 pr-4 bg-white border border-slate-200 rounded-md text-sm font-medium focus:outline-none focus:border-[#0a66c2] focus:ring-1 focus:ring-[#0a66c2] transition-all text-slate-800 placeholder-slate-400"
                            />
                        </div>

                        <div className="flex items-center gap-2 text-sm text-slate-600 font-semibold self-end sm:self-auto">
                            <span>Showing {users.from || 0} - {users.to || 0} of {users.total || 0} users</span>
                        </div>
                    </div>

                    {/* USERS TABLE WITH 7 EXTENDED COLUMNS & HIGH LEGIBILITY */}
                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse">
                            <thead>
                                <tr className="border-b border-slate-200 bg-slate-50 text-xs font-bold text-slate-700">
                                    <th className="px-5 py-3">User Profile</th>
                                    <th className="px-5 py-3">Contact Phone</th>
                                    <th className="px-5 py-3">Role & Account</th>
                                    <th className="px-5 py-3">Bookings</th>
                                    <th className="px-5 py-3">Total Spent</th>
                                    <th className="px-5 py-3">Joined Date</th>
                                    <th className="px-5 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 text-sm">
                                {users.data.length > 0 ? (
                                    users.data.map((u) => (
                                        <tr
                                            key={u.id}
                                            className="hover:bg-slate-50/80 transition-colors group"
                                        >
                                            {/* Column 1: Profile (Photo, Name, Email) */}
                                            <td className="px-5 py-3.5">
                                                <div className="flex items-center gap-3">
                                                    <div className="w-10 h-10 rounded-full bg-[#0a66c2]/10 text-[#0a66c2] font-bold text-xs flex items-center justify-center border border-[#0a66c2]/20 shrink-0">
                                                        {u.profile_photo_url ? (
                                                            <img
                                                                src={u.profile_photo_url}
                                                                alt=""
                                                                className="w-full h-full rounded-full object-cover"
                                                            />
                                                        ) : (
                                                            u.name ? u.name.slice(0, 2) : <UserIcon size={18} />
                                                        )}
                                                    </div>
                                                    <div>
                                                        <p className="font-bold text-slate-900 group-hover:text-[#0a66c2] transition-colors leading-tight text-sm">
                                                            {u.name}
                                                        </p>
                                                        <div className="flex items-center gap-1.5 text-xs text-slate-500 font-medium mt-0.5">
                                                            <Mail size={13} className="text-slate-400 shrink-0" />
                                                            {u.email}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            {/* Column 2: Phone & Verification */}
                                            <td className="px-5 py-3.5">
                                                <div className="space-y-1">
                                                    <div className="flex items-center gap-1.5 text-slate-800 font-semibold text-xs">
                                                        <Phone size={13} className="text-slate-400 shrink-0" />
                                                        {u.phone || "N/A"}
                                                    </div>
                                                    <div>
                                                        {u.is_verified ? (
                                                            <span className="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                                                <CheckCircle2 size={11} /> Verified
                                                            </span>
                                                        ) : (
                                                            <span className="inline-flex items-center gap-1 text-xs font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">
                                                                <XCircle size={11} /> Unverified
                                                            </span>
                                                        )}
                                                    </div>
                                                </div>
                                            </td>

                                            {/* Column 3: Role & Permissions */}
                                            <td className="px-5 py-3.5">
                                                {u.is_admin ? (
                                                    <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-purple-50 text-purple-700 text-xs font-bold border border-purple-200">
                                                        <Shield size={13} /> Administrator
                                                    </span>
                                                ) : (
                                                    <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200">
                                                        <UserIcon size={13} /> Client / Customer
                                                    </span>
                                                )}
                                            </td>

                                            {/* Column 4: Bookings Count */}
                                            <td className="px-5 py-3.5">
                                                <div className="flex items-center gap-1.5 font-bold text-slate-900 text-sm">
                                                    <ShoppingBag size={15} className="text-slate-400 shrink-0" />
                                                    {u.bookings_count} Bookings
                                                </div>
                                            </td>

                                            {/* Column 5: Total Spent */}
                                            <td className="px-5 py-3.5">
                                                <div className="flex items-center gap-1 font-bold text-slate-900 text-sm">
                                                    ${Number(u.total_spent || 0).toFixed(2)}
                                                </div>
                                            </td>

                                            {/* Column 6: Joined Date */}
                                            <td className="px-5 py-3.5 text-slate-700">
                                                <div className="flex items-center gap-1.5 text-xs font-medium">
                                                    <Calendar size={13} className="text-slate-400 shrink-0" />
                                                    {u.created_at
                                                        ? new Date(u.created_at).toLocaleDateString("en-US", {
                                                              month: "short",
                                                              day: "numeric",
                                                              year: "numeric",
                                                          })
                                                        : "N/A"}
                                                </div>
                                            </td>

                                            {/* Column 7: Actions */}
                                            <td className="px-5 py-3.5 text-right">
                                                <div className="flex items-center justify-end gap-2">
                                                    {u.id !== auth.user.id && (
                                                        <button
                                                            onClick={() => handleDelete(u.id)}
                                                            className="w-8 h-8 flex items-center justify-center rounded-md text-rose-600 hover:bg-rose-50 transition-all border border-slate-200 hover:border-rose-200"
                                                            title="Delete User"
                                                        >
                                                            <Trash2 size={15} />
                                                        </button>
                                                    )}
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="7" className="px-5 py-14 text-center text-slate-400 text-sm">
                                            No users found matching your query.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* COMPACT PAGINATION FOOTER */}
                    <div className="flex flex-col sm:flex-row items-center justify-between gap-3 px-5 py-3 border-t border-slate-200 bg-slate-50/50">
                        <div className="flex items-center gap-2 text-xs text-slate-600">
                            <span>Rows per page:</span>
                            <div className="relative">
                                <select
                                    value={filters.per_page || 10}
                                    onChange={handlePerPageChange}
                                    className="h-8 pl-3 pr-8 bg-white border border-slate-200 rounded-md text-xs font-semibold text-slate-800 appearance-none cursor-pointer focus:border-[#0a66c2] outline-none"
                                >
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                </select>
                              
                            </div>
                        </div>

                        <div className="flex items-center gap-4 text-xs font-semibold text-slate-700">
                            <span>
                                {users.from || 0} - {users.to || 0} of {users.total || 0}
                            </span>
                            <div className="flex items-center gap-1">
                                <button
                                    onClick={() => handlePageChange(users.prev_page_url)}
                                    disabled={!users.prev_page_url}
                                    className="w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
                                >
                                    <ChevronLeft size={16} />
                                </button>
                                <button
                                    onClick={() => handlePageChange(users.next_page_url)}
                                    disabled={!users.next_page_url}
                                    className="w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
                                >
                                    <ChevronRight size={16} />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
