import React, { useState } from "react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Head, router } from "@inertiajs/react";
import {
    Search,
    Trash2,
    ChevronDown,
    ChevronLeft,
    ChevronRight,
    Mail,
    Calendar,
    Phone,
    User as UserIcon,
    MessageSquare,
} from "lucide-react";

export default function Index({ contacts, filters = {} }) {
    const [search, setSearch] = useState(filters.search || "");

    const handleSearch = (value) => {
        setSearch(value);
        updateFilters({ search: value, page: 1 });
    };

    const updateFilters = (newFilters) => {
        router.get(
            route("admin.contacts.index"),
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
        if (confirm("Are you sure you want to delete this contact message?")) {
            router.delete(route("admin.contacts.destroy", id));
        }
    };

    return (
        <AdminLayout>
            <Head title="Contact Messages" />

            <div className="space-y-4 max-w-[1600px] mx-auto pb-12">
                {/* COMPACT TOP HEADER */}
                <div className="bg-white rounded-md p-5 border border-slate-200/80 shadow-2xs flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-md bg-[#0a66c2]/10 text-[#0a66c2] flex items-center justify-center border border-[#0a66c2]/20">
                            <Mail size={22} />
                        </div>
                        <div>
                            <h1 className="text-xl font-bold text-slate-900 leading-tight">
                                Contact Messages
                            </h1>
                            <p className="text-sm text-slate-600 mt-0.5">
                                View, search, and manage incoming user inquiries and contact requests.
                            </p>
                        </div>
                    </div>

                    <div className="text-sm font-semibold text-slate-700 bg-slate-100 px-3.5 py-1.5 rounded-md border border-slate-200">
                        Total Messages: <strong className="text-slate-900">{contacts.total || 0}</strong>
                    </div>
                </div>

                {/* MAIN TABLE CONTAINER */}
                <div className="bg-white rounded-md border border-slate-200/80 shadow-2xs overflow-hidden">
                    {/* SEARCH & FILTER BAR */}
                    <div className="p-4 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div className="relative w-full sm:w-[350px]">
                            <div className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                                <Search size={17} />
                            </div>
                            <input
                                type="text"
                                value={search}
                                onChange={(e) => handleSearch(e.target.value)}
                                placeholder="Search by name or email address..."
                                className="w-full h-10 pl-10 pr-4 bg-white border border-slate-200 rounded-md text-sm font-medium focus:outline-none focus:border-[#0a66c2] focus:ring-1 focus:ring-[#0a66c2] transition-all text-slate-800 placeholder-slate-400"
                            />
                        </div>

                        <div className="text-sm text-slate-600 font-semibold self-end sm:self-auto">
                            Showing {contacts.from || 0} - {contacts.to || 0} of {contacts.total || 0} messages
                        </div>
                    </div>

                    {/* CONTACT MESSAGES TABLE WITH HIGH LEGIBILITY */}
                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse">
                            <thead>
                                <tr className="border-b border-slate-200 bg-slate-50 text-xs font-bold text-slate-700">
                                    <th className="px-5 py-3">Requester Info</th>
                                    <th className="px-5 py-3">Message Content</th>
                                    <th className="px-5 py-3">Received Date</th>
                                    <th className="px-5 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 text-sm">
                                {contacts.data.length > 0 ? (
                                    contacts.data.map((contact) => (
                                        <tr
                                            key={contact.id}
                                            className="hover:bg-slate-50/80 transition-colors group"
                                        >
                                            {/* Requester Info */}
                                            <td className="px-5 py-3.5 align-top">
                                                <div className="flex items-start gap-3">
                                                    <div className="w-9 h-9 rounded-md bg-[#0a66c2]/10 text-[#0a66c2] flex items-center justify-center border border-[#0a66c2]/20 font-bold shrink-0">
                                                        <UserIcon size={18} />
                                                    </div>
                                                    <div>
                                                        <p className="font-bold text-slate-900 group-hover:text-[#0a66c2] transition-colors leading-tight text-sm">
                                                            {contact.first_name} {contact.last_name}
                                                        </p>
                                                        <div className="flex flex-col gap-0.5 mt-1 text-xs text-slate-600 font-medium">
                                                            <div className="flex items-center gap-1.5">
                                                                <Mail size={13} className="text-slate-400 shrink-0" />
                                                                {contact.email}
                                                            </div>
                                                            {contact.phone_number && (
                                                                <div className="flex items-center gap-1.5 text-slate-500">
                                                                    <Phone size={13} className="text-slate-400 shrink-0" />
                                                                    {contact.phone_number}
                                                                </div>
                                                            )}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            {/* Message */}
                                            <td className="px-5 py-3.5 align-top max-w-[450px]">
                                                <div className="flex items-start gap-2">
                                                    <MessageSquare size={16} className="text-slate-400 shrink-0 mt-0.5" />
                                                    <p className="text-sm text-slate-800 leading-relaxed whitespace-pre-wrap font-normal">
                                                        {contact.message}
                                                    </p>
                                                </div>
                                            </td>

                                            {/* Date */}
                                            <td className="px-5 py-3.5 align-top text-slate-700">
                                                <div className="flex items-center gap-1.5 text-xs font-medium">
                                                    <Calendar size={14} className="text-slate-400 shrink-0" />
                                                    {contact.created_at
                                                        ? new Date(contact.created_at).toLocaleDateString("en-US", {
                                                              year: "numeric",
                                                              month: "short",
                                                              day: "numeric",
                                                          })
                                                        : "N/A"}
                                                </div>
                                            </td>

                                            {/* Action */}
                                            <td className="px-5 py-3.5 text-right align-top">
                                                <button
                                                    onClick={() => handleDelete(contact.id)}
                                                    className="w-8 h-8 flex items-center justify-center rounded-md text-rose-600 hover:bg-rose-50 border border-slate-200 hover:border-rose-200 transition-all ml-auto"
                                                    title="Delete Message"
                                                >
                                                    <Trash2 size={15} />
                                                </button>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="4" className="px-5 py-14 text-center text-slate-400 text-sm">
                                            No contact messages found matching your query.
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
                                {contacts.from || 0} - {contacts.to || 0} of {contacts.total || 0}
                            </span>
                            <div className="flex items-center gap-1">
                                <button
                                    onClick={() => handlePageChange(contacts.prev_page_url)}
                                    disabled={!contacts.prev_page_url}
                                    className="w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
                                >
                                    <ChevronLeft size={16} />
                                </button>
                                <button
                                    onClick={() => handlePageChange(contacts.next_page_url)}
                                    disabled={!contacts.next_page_url}
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
