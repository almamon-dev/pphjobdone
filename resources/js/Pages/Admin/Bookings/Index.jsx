import React, { useState } from "react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Head, Link } from "@inertiajs/react";
import {
    FolderKanban,
    Search,
    Settings,
    Calendar,
    DollarSign,
    CheckCircle2,
    Clock,
    AlertCircle,
    ShoppingBag,
} from "lucide-react";

export default function BookingsIndex({ bookings = [] }) {
    const [search, setSearch] = useState("");

    const filteredBookings = bookings.filter((booking) => {
        const query = search.toLowerCase();
        const clientName = booking.user?.name?.toLowerCase() || "";
        const clientEmail = booking.user?.email?.toLowerCase() || "";
        const serviceTitle = booking.service?.title?.toLowerCase() || "";
        const planName = booking.plan_name?.toLowerCase() || "";
        const idStr = `bkg-${booking.id}`.toLowerCase();

        return (
            clientName.includes(query) ||
            clientEmail.includes(query) ||
            serviceTitle.includes(query) ||
            planName.includes(query) ||
            idStr.includes(query)
        );
    });

    return (
        <AdminLayout>
            <Head title="Client Bookings" />

            <div className="space-y-4 max-w-[1600px] mx-auto pb-12">
                {/* COMPACT TOP HEADER */}
                <div className="bg-white rounded-md p-5 border border-slate-200/80 shadow-2xs flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-md bg-[#0a66c2]/10 text-[#0a66c2] flex items-center justify-center border border-[#0a66c2]/20">
                            <FolderKanban size={22} />
                        </div>
                        <div>
                            <h1 className="text-xl font-bold text-slate-900 leading-tight">
                                Client Bookings
                            </h1>
                            <p className="text-sm text-slate-600 mt-0.5">
                                Track client project progress, milestone tasks, and order statuses.
                            </p>
                        </div>
                    </div>

                    <div className="text-sm font-semibold text-slate-700 bg-slate-100 px-3.5 py-1.5 rounded-md border border-slate-200">
                        Total Bookings: <strong className="text-slate-900">{bookings.length}</strong>
                    </div>
                </div>

                {/* MAIN TABLE CONTAINER */}
                <div className="bg-white rounded-md border border-slate-200/80 shadow-2xs overflow-hidden">
                    {/* SEARCH & FILTER BAR */}
                    <div className="p-4 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div className="relative w-full sm:w-[360px]">
                            <div className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                                <Search size={17} />
                            </div>
                            <input
                                type="text"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Search by client, service, or booking ID..."
                                className="w-full h-10 pl-10 pr-4 bg-white border border-slate-200 rounded-md text-sm font-medium focus:outline-none focus:border-[#0a66c2] focus:ring-1 focus:ring-[#0a66c2] transition-all text-slate-800 placeholder-slate-400"
                            />
                        </div>

                        <div className="text-sm text-slate-600 font-semibold self-end sm:self-auto">
                            Showing <strong className="text-slate-900">{filteredBookings.length}</strong> of {bookings.length} orders
                        </div>
                    </div>

                    {/* BOOKINGS TABLE */}
                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse">
                            <thead>
                                <tr className="border-b border-slate-200 bg-slate-50 text-xs font-bold text-slate-700">
                                    <th className="px-5 py-3">Client Profile & Order</th>
                                    <th className="px-5 py-3">Service / Package</th>
                                    <th className="px-5 py-3">Booking Date</th>
                                    <th className="px-5 py-3">Project Status</th>
                                    <th className="px-5 py-3">Payment Status</th>
                                    <th className="px-5 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 text-sm">
                                {filteredBookings.length > 0 ? (
                                    filteredBookings.map((booking) => (
                                        <tr
                                            key={booking.id}
                                            className="hover:bg-slate-50/80 transition-colors group"
                                        >
                                            {/* Column 1: Client Name & BKG ID */}
                                            <td className="px-5 py-3.5">
                                                <div className="flex items-center gap-3">
                                                    <div className="w-9 h-9 rounded-md bg-[#0a66c2]/10 text-[#0a66c2] flex items-center justify-center border border-[#0a66c2]/20 font-bold shrink-0">
                                                        <ShoppingBag size={18} />
                                                    </div>
                                                    <div>
                                                        <p className="font-bold text-slate-900 group-hover:text-[#0a66c2] transition-colors leading-tight text-sm">
                                                            {booking.user?.name || "Unknown Client"}
                                                        </p>
                                                        <p className="text-xs text-slate-500 font-mono mt-0.5">
                                                            BKG-#{booking.id}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>

                                            {/* Column 2: Service & Plan */}
                                            <td className="px-5 py-3.5">
                                                <div>
                                                    <p className="font-bold text-slate-900 leading-tight text-sm">
                                                        {booking.service?.title || "Custom Service"}
                                                    </p>
                                                    <div className="flex items-center gap-2 mt-0.5">
                                                        <span className="text-xs text-slate-600 font-medium bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">
                                                            {booking.plan_name || "Standard Plan"}
                                                        </span>
                                                        <span className="text-xs font-bold text-slate-900">
                                                            ${Number(booking.price || 0).toFixed(2)}
                                                        </span>
                                                    </div>
                                                    {(booking.website_url || booking.campaign_details?.website_url || booking.campaign_details?.links) && (
                                                        <p className="text-[11px] text-[#0a66c2] truncate max-w-[240px] font-medium mt-1">
                                                            🔗 {booking.website_url || booking.campaign_details?.website_url || booking.campaign_details?.links}
                                                        </p>
                                                    )}
                                                </div>
                                            </td>

                                            {/* Column 3: Date */}
                                            <td className="px-5 py-3.5 text-slate-700">
                                                <div className="flex items-center gap-1.5 text-xs font-medium">
                                                    <Calendar size={14} className="text-slate-400 shrink-0" />
                                                    {booking.created_at
                                                        ? new Date(booking.created_at).toLocaleDateString("en-US", {
                                                              month: "short",
                                                              day: "numeric",
                                                              year: "numeric",
                                                          })
                                                        : "N/A"}
                                                </div>
                                            </td>

                                            {/* Column 4: Status */}
                                            <td className="px-5 py-3.5">
                                                <span
                                                    className={`inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold capitalize border ${
                                                        booking.status === "completed"
                                                            ? "bg-emerald-50 text-emerald-700 border-emerald-200"
                                                            : booking.status === "ongoing" || booking.status === "active"
                                                            ? "bg-[#0a66c2]/10 text-[#0a66c2] border-[#0a66c2]/20"
                                                            : booking.status === "pending"
                                                            ? "bg-amber-50 text-amber-700 border-amber-200"
                                                            : "bg-slate-100 text-slate-700 border-slate-200"
                                                    }`}
                                                >
                                                    {booking.status === "completed" ? (
                                                        <CheckCircle2 size={13} />
                                                    ) : booking.status === "ongoing" || booking.status === "active" ? (
                                                        <Clock size={13} />
                                                    ) : (
                                                        <AlertCircle size={13} />
                                                    )}
                                                    {booking.status || "Pending"}
                                                </span>
                                            </td>

                                            {/* Column 5: Payment Status */}
                                            <td className="px-5 py-3.5">
                                                <span
                                                    className={`inline-flex items-center px-2 py-1 rounded-md text-xs font-bold border ${
                                                        booking.payment_status === "paid"
                                                            ? "bg-emerald-50 text-emerald-700 border-emerald-200"
                                                            : "bg-rose-50 text-rose-700 border-rose-200"
                                                    }`}
                                                >
                                                    {booking.payment_status === "paid" ? "Paid" : "Unpaid"}
                                                </span>
                                            </td>

                                            {/* Column 6: Actions */}
                                            <td className="px-5 py-3.5 text-right">
                                                <div className="flex items-center justify-end">
                                                    <Link
                                                        href={`/admin/bookings/${booking.id}`}
                                                        className="px-3 py-1.5 rounded-md text-xs font-semibold text-[#0a66c2] bg-[#0a66c2]/10 hover:bg-[#0a66c2] hover:text-white transition-all border border-[#0a66c2]/20 flex items-center gap-1.5 shadow-2xs"
                                                    >
                                                        <Settings size={14} /> Manage Tasks
                                                    </Link>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="6" className="px-5 py-14 text-center text-slate-400 text-sm">
                                            No client bookings found matching your search.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
