import React, { useState } from "react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Head, Link, router } from "@inertiajs/react";
import {
    Zap,
    Plus,
    Search,
    Trash2,
    Edit2,
    Copy,
    ChevronDown,
    ChevronLeft,
    ChevronRight,
    Briefcase,
    Layers,
} from "lucide-react";

export default function Index({ campaigns, filters = {} }) {
    const [search, setSearch] = useState(filters.search || "");

    const handleSearch = (value) => {
        setSearch(value);
        updateFilters({ search: value, page: 1 });
    };

    const updateFilters = (newFilters) => {
        router.get(
            route("admin.campaigns.index"),
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
        if (confirm("Are you sure you want to delete this campaign?")) {
            router.delete(route("admin.campaigns.destroy", id));
        }
    };

    const handleDuplicate = (id) => {
        router.post(route("admin.campaigns.duplicate", id));
    };

    return (
        <AdminLayout>
            <Head title="Campaign Management" />

            <div className="space-y-4 max-w-[1600px] mx-auto pb-12">
                {/* COMPACT TOP HEADER */}
                <div className="bg-white rounded-md p-5 border border-slate-200/80 shadow-2xs flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-md bg-[#0a66c2]/10 text-[#0a66c2] flex items-center justify-center border border-[#0a66c2]/20">
                            <Zap size={22} />
                        </div>
                        <div>
                            <h1 className="text-xl font-bold text-slate-900 leading-tight">
                                Campaign Management
                            </h1>
                            <p className="text-sm text-slate-600 mt-0.5">
                                Manage promotional campaign groups, tier pricing, and feature bundles.
                            </p>
                        </div>
                    </div>

                    <Link
                        href={route("admin.campaigns.create")}
                        className="px-4 py-2 bg-[#0a66c2] hover:bg-[#084e96] text-white rounded-md text-sm font-semibold transition-all flex items-center gap-1.5 shadow-2xs"
                    >
                        <Plus size={16} /> Add New Campaign
                    </Link>
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
                                placeholder="Search campaigns by title or group..."
                                className="w-full h-10 pl-10 pr-4 bg-white border border-slate-200 rounded-md text-sm font-medium focus:outline-none focus:border-[#0a66c2] focus:ring-1 focus:ring-[#0a66c2] transition-all text-slate-800 placeholder-slate-400"
                            />
                        </div>

                        <div className="text-sm text-slate-600 font-semibold self-end sm:self-auto">
                            Total Campaigns: <strong className="text-slate-900">{campaigns.total || 0}</strong>
                        </div>
                    </div>

                    {/* COMPACT TABLE WITH CLEAR FONT SIZES */}
                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse">
                            <thead>
                                <tr className="border-b border-slate-200 bg-slate-50 text-xs font-bold text-slate-700">
                                    <th className="px-5 py-3">Campaign Title</th>
                                    <th className="px-5 py-3">Assigned Service</th>
                                    <th className="px-5 py-3">Pricing Tiers</th>
                                    <th className="px-5 py-3">Status</th>
                                    <th className="px-5 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 text-sm">
                                {campaigns.data.length > 0 ? (
                                    campaigns.data.map((campaign) => (
                                        <tr
                                            key={campaign.id}
                                            className="hover:bg-slate-50/80 transition-colors group"
                                        >
                                            <td className="px-5 py-3.5">
                                                <div className="flex items-center gap-3">
                                                    <div className="w-9 h-9 rounded-md bg-[#0a66c2]/10 text-[#0a66c2] flex items-center justify-center border border-[#0a66c2]/20 shrink-0">
                                                        <Zap size={18} />
                                                    </div>
                                                    <div className="min-w-0">
                                                        <p className="font-bold text-slate-900 group-hover:text-[#0a66c2] transition-colors leading-tight text-sm">
                                                            {campaign.title}
                                                        </p>
                                                        {campaign.subtitle && (
                                                            <p className="text-xs text-slate-500 truncate mt-0.5 max-w-[280px]">
                                                                {campaign.subtitle}
                                                            </p>
                                                        )}
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-5 py-3.5">
                                                <div className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 text-slate-800 text-xs font-semibold border border-slate-200">
                                                    <Briefcase size={13} className="text-[#0a66c2]" />
                                                    {campaign.service?.title || "N/A"}
                                                </div>
                                            </td>
                                            <td className="px-5 py-3.5">
                                                <div className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 text-slate-800 text-xs font-semibold border border-slate-200">
                                                    <Layers size={13} className="text-slate-400" />
                                                    {campaign.tiers?.length || 0} Tiers
                                                </div>
                                            </td>
                                            <td className="px-5 py-3.5">
                                                <span
                                                    className={`inline-flex items-center px-2 py-1 rounded-md text-xs font-bold ${
                                                        campaign.status
                                                            ? "bg-emerald-50 text-emerald-700 border border-emerald-200"
                                                            : "bg-slate-100 text-slate-700 border border-slate-200"
                                                    }`}
                                                >
                                                    {campaign.status ? "Active" : "Inactive"}
                                                </span>
                                            </td>
                                            <td className="px-5 py-3.5 text-right">
                                                <div className="flex items-center justify-end gap-2">
                                                    <button
                                                        onClick={() => handleDuplicate(campaign.id)}
                                                        className="w-8 h-8 flex items-center justify-center rounded-md text-[#0a66c2] hover:bg-[#0a66c2]/10 border border-slate-200 hover:border-[#0a66c2]/20 transition-all"
                                                        title="Duplicate"
                                                    >
                                                        <Copy size={15} />
                                                    </button>
                                                    <Link
                                                        href={route("admin.campaigns.edit", campaign.id)}
                                                        className="w-8 h-8 flex items-center justify-center rounded-md text-amber-600 hover:bg-amber-50 border border-slate-200 hover:border-amber-200 transition-all"
                                                        title="Edit"
                                                    >
                                                        <Edit2 size={15} />
                                                    </Link>
                                                    <button
                                                        onClick={() => handleDelete(campaign.id)}
                                                        className="w-8 h-8 flex items-center justify-center rounded-md text-rose-600 hover:bg-rose-50 border border-slate-200 hover:border-rose-200 transition-all"
                                                        title="Delete"
                                                    >
                                                        <Trash2 size={15} />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="5" className="px-5 py-14 text-center text-slate-400 text-sm">
                                            No campaigns found matching your query.
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
                                {campaigns.from || 0} - {campaigns.to || 0} of {campaigns.total || 0}
                            </span>
                            <div className="flex items-center gap-1">
                                <button
                                    onClick={() => handlePageChange(campaigns.prev_page_url)}
                                    disabled={!campaigns.prev_page_url}
                                    className="w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
                                >
                                    <ChevronLeft size={16} />
                                </button>
                                <button
                                    onClick={() => handlePageChange(campaigns.next_page_url)}
                                    disabled={!campaigns.next_page_url}
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
