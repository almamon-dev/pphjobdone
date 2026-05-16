import React, { useState } from "react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Head, Link, router } from "@inertiajs/react";
import {
    Home,
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
            { preserveState: true, replace: true },
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

            <div className="space-y-6 max-w-full mx-auto pb-20">
                {/* Top Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <h1 className="text-[24px] font-bold text-[#2f3344] tracking-tight">
                            Campaign Management
                        </h1>
                        <div className="flex items-center gap-2 text-[13px] text-[#727586] mt-1">
                            <Home size={16} className="text-[#727586]" />
                            <span className="text-[#c3c4ca]">-</span>
                            <span>Service campaigns group</span>
                        </div>
                    </div>
                    <Link
                        href={route("admin.campaigns.create")}
                        className="bg-[#673ab7] text-white px-6 py-2.5 rounded-[8px] text-[13px] font-bold hover:bg-[#5e35b1] transition-all flex items-center gap-2 shadow-sm"
                    >
                        <Plus size={18} />
                        Add New Campaign
                    </Link>
                </div>

                {/* Main Content Card */}
                <div className="bg-white rounded-[12px] border border-[#e3e4e8] shadow-sm overflow-hidden">
                    {/* Search Bar */}
                    <div className="p-7">
                        <div className="relative w-full">
                            <div className="absolute left-5 top-1/2 -translate-y-1/2 text-[#a0a3af]">
                                <Search size={22} />
                            </div>
                            <input
                                type="text"
                                value={search}
                                onChange={(e) => handleSearch(e.target.value)}
                                placeholder="Search campaigns by title or group..."
                                className="w-full h-[52px] pl-14 pr-6 bg-white border border-[#e3e4e8] rounded-[8px] text-[15px] focus:outline-none focus:border-[#673ab7] focus:ring-1 focus:ring-[#673ab7] transition-all"
                            />
                        </div>
                    </div>

                    {/* Table Area */}
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-[#e3e4e8]">
                                    <th className="text-left px-7 py-4 text-[13px] font-bold text-[#2f3344] uppercase tracking-wider bg-[#fafbfc]">
                                        Campaign Group
                                    </th>
                                    <th className="text-left px-5 py-4 text-[13px] font-bold text-[#2f3344] uppercase tracking-wider bg-[#fafbfc]">
                                        Service
                                    </th>
                                    <th className="text-left px-5 py-4 text-[13px] font-bold text-[#2f3344] uppercase tracking-wider bg-[#fafbfc]">
                                        Price Tiers
                                    </th>
                                    <th className="text-left px-5 py-4 text-[13px] font-bold text-[#2f3344] uppercase tracking-wider bg-[#fafbfc]">
                                        Status
                                    </th>
                                    <th className="px-7 py-4 text-right bg-[#fafbfc]">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#f1f2f4]">
                                {campaigns.data.length > 0 ? (
                                    campaigns.data.map((campaign) => (
                                        <tr
                                            key={campaign.id}
                                            className="hover:bg-[#fafbfc] transition-colors group"
                                        >
                                            <td className="px-7 py-5">
                                                <div className="flex items-center gap-4">
                                                    <div className="w-10 h-10 rounded-lg bg-[#f4f0ff] flex items-center justify-center text-[#673ab7] border border-[#e9e3ff]">
                                                        <Zap size={20} />
                                                    </div>
                                                    <div>
                                                        <p className="text-[14px] font-bold text-[#2f3344] group-hover:text-[#673ab7] transition-colors">
                                                            {campaign.title}
                                                        </p>
                                                        <p className="text-[12px] text-[#727586] font-medium mt-0.5 line-clamp-1 max-w-[250px]">
                                                            {campaign.subtitle}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-5 py-5">
                                                <div className="flex items-center gap-2">
                                                    <div className="px-2.5 py-1 rounded-full bg-slate-100 text-[#2f3344] text-[11px] font-bold flex items-center gap-1.5 ring-1 ring-slate-200">
                                                        <Briefcase size={12} className="text-[#673ab7]" />
                                                        {campaign.service?.title}
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-5 py-5">
                                                <div className="flex items-center gap-2">
                                                    <div className="px-2.5 py-1 rounded-lg bg-[#fafbfc] text-[#2f3344] text-[12px] font-bold border border-[#e3e4e8] flex items-center gap-2">
                                                        <Layers size={14} className="text-[#a0a3af]" />
                                                        {campaign.tiers?.length || 0} Tiers
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-5 py-5">
                                                <span
                                                    className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border ${campaign.status
                                                            ? "bg-green-50 text-green-600 border-green-100"
                                                            : "bg-red-50 text-red-600 border-red-100"
                                                        }`}
                                                >
                                                    {campaign.status ? "Active" : "Inactive"}
                                                </span>
                                            </td>
                                            <td className="px-7 py-5 text-right">
                                                <div className="flex items-center justify-end gap-2">
                                                    <button
                                                        onClick={() => handleDuplicate(campaign.id)}
                                                        className="w-[32px] h-[32px] flex items-center justify-center rounded-[6px] text-[#673ab7] bg-[#f4f0ff] hover:bg-[#673ab7] hover:text-white transition-all shadow-sm border border-transparent"
                                                        title="Duplicate"
                                                    >
                                                        <Copy size={16} />
                                                    </button>
                                                    <Link
                                                        href={route("admin.campaigns.edit", campaign.id)}
                                                        className="w-[32px] h-[32px] flex items-center justify-center rounded-[6px] text-[#fbbf24] bg-[#fffbeb] hover:bg-[#fbbf24] hover:text-white transition-all shadow-sm border border-transparent"
                                                        title="Edit"
                                                    >
                                                        <Edit2 size={16} />
                                                    </Link>
                                                    <button
                                                        onClick={() => handleDelete(campaign.id)}
                                                        className="w-[32px] h-[32px] flex items-center justify-center rounded-[6px] text-[#ef4444] bg-[#fee2e2]/50 hover:bg-[#ef4444] hover:text-white transition-all shadow-sm border border-transparent"
                                                        title="Delete"
                                                    >
                                                        <Trash2 size={16} />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="5" className="px-7 py-24 text-center">
                                            <div className="flex flex-col items-center gap-3 text-[#727586]">
                                                <div className="w-16 h-16 bg-[#f8f9fa] rounded-full flex items-center justify-center mb-2">
                                                    <Zap size={30} className="text-[#c3c4ca]" />
                                                </div>
                                                <p className="text-[16px] font-bold text-[#2f3344]">No campaigns found</p>
                                                <p className="text-[14px]">Try creating a new campaign group.</p>
                                            </div>
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination */}
                    <div className="flex items-center justify-end gap-8 px-8 py-5 border-t border-[#e3e4e8]">
                        <div className="flex items-center gap-3">
                            <span className="text-[13px] text-[#727586]">Items per page:</span>
                            <div className="relative">
                                <select
                                    value={filters.per_page || 10}
                                    onChange={handlePerPageChange}
                                    className="h-[38px] pl-4 pr-10 bg-white border border-[#e3e4e8] rounded-[6px] text-[13px] text-[#2f3344] font-medium appearance-none cursor-pointer focus:border-[#673ab7] outline-none"
                                >
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                                <div className="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[#727586]">
                                    <ChevronDown size={14} />
                                </div>
                            </div>
                        </div>

                        <div className="flex items-center gap-6">
                            <span className="text-[13px] text-[#2f3344] font-medium">
                                {campaigns.from || 0} - {campaigns.to || 0} of {campaigns.total || 0}
                            </span>
                            <div className="flex gap-2">
                                <button
                                    onClick={() => handlePageChange(campaigns.prev_page_url)}
                                    disabled={!campaigns.prev_page_url}
                                    className="w-[34px] h-[34px] flex items-center justify-center rounded-full text-[#673ab7] hover:bg-[#673ab7]/10 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
                                >
                                    <ChevronLeft size={20} />
                                </button>
                                <button
                                    onClick={() => handlePageChange(campaigns.next_page_url)}
                                    disabled={!campaigns.next_page_url}
                                    className="w-[34px] h-[34px] flex items-center justify-center rounded-full text-[#673ab7] hover:bg-[#673ab7]/10 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
                                >
                                    <ChevronRight size={20} />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
