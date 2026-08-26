import React, { useState } from "react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Head, Link, router } from "@inertiajs/react";
import {
    Search,
    Trash2,
    Edit,
    Plus,
    ChevronDown,
    ChevronLeft,
    ChevronRight,
    Briefcase,
    Zap,
} from "lucide-react";

export default function Index({ services, filters = {}, auth }) {
    const [search, setSearch] = useState(filters.search || "");

    const handleSearch = (value) => {
        setSearch(value);
        updateFilters({ search: value, page: 1 });
    };

    const updateFilters = (newFilters) => {
        router.get(
            route("admin.services.index"),
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
        if (confirm("Are you sure you want to delete this service?")) {
            router.delete(route("admin.services.destroy", id));
        }
    };

    return (
        <AdminLayout>
            <Head title="Service Management" />

            <div className="space-y-4 max-w-[1600px] mx-auto pb-12">
                {/* TOP HEADER */}
                <div className="bg-white rounded-md p-5 border border-slate-200/80 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-md bg-[#0a66c2]/10 text-[#0a66c2] flex items-center justify-center border border-[#0a66c2]/20">
                            <Briefcase size={22} />
                        </div>
                        <div>
                            <h1 className="text-xl font-bold text-slate-900 leading-tight">
                                Service Management
                            </h1>
                            <p className="text-sm text-slate-600 mt-0.5">
                                Manage website services, pricing categories, and promotional campaigns.
                            </p>
                        </div>
                    </div>

                    <Link
                        href={route("admin.services.create")}
                        className="px-4 py-2 bg-[#0a66c2] hover:bg-[#084e96] text-white rounded-md text-sm font-semibold transition-all flex items-center gap-1.5 shadow-2xs"
                    >
                        <Plus size={16} /> Add Service
                    </Link>
                </div>

                {/* MAIN TABLE CARD */}
                <div className="bg-white rounded-md border border-slate-200/80 shadow-2xs overflow-hidden">
                    {/* SEARCH BAR */}
                    <div className="p-4 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div className="relative w-full sm:w-[350px]">
                            <div className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                                <Search size={17} />
                            </div>
                            <input
                                type="text"
                                value={search}
                                onChange={(e) => handleSearch(e.target.value)}
                                placeholder="Search by title, subtitle, or slug..."
                                className="w-full h-10 pl-10 pr-4 bg-white border border-slate-200 rounded-md text-sm font-medium focus:outline-none focus:border-[#0a66c2] focus:ring-1 focus:ring-[#0a66c2] transition-all text-slate-800 placeholder-slate-400"
                            />
                        </div>

                        <div className="text-sm text-slate-600 font-semibold self-end sm:self-auto">
                            Total Services: <strong className="text-slate-900">{services.total || 0}</strong>
                        </div>
                    </div>

                    {/* TABLE WITH CLEAR FONT SIZES */}
                    <div className="overflow-x-auto">
<div className="w-full overflow-x-auto overflow-y-hidden touch-pan-x border border-slate-200/80 rounded-xl shadow-2xs mb-4">
                        <table className="w-full min-w-[850px] text-left border-collapse">
                            <thead>
                                <tr className="border-b border-slate-200 bg-slate-50 text-xs font-bold text-slate-700">
                                    <th className="px-5 py-3 whitespace-nowrap">Service Name & Slug</th>
                                    <th className="px-5 py-3 whitespace-nowrap">Subtitle</th>
                                    <th className="px-5 py-3 whitespace-nowrap">Type</th>
                                    <th className="px-5 py-3 whitespace-nowrap">Status</th>
                                    <th className="px-5 py-3 text-right whitespace-nowrap">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 text-sm">
                                {services.data.length > 0 ? (
                                    services.data.map((service) => (
                                        <tr
                                            key={service.id}
                                            className="hover:bg-slate-50/80 transition-colors group"
                                        >
                                            <td className="px-5 py-3.5 whitespace-nowrap">
                                                <div className="flex items-center gap-3">
                                                    <div className="w-9 h-9 rounded-md bg-[#0a66c2]/10 text-[#0a66c2] flex items-center justify-center border border-[#0a66c2]/20 overflow-hidden shrink-0">
                                                        {service.icon ? (
                                                            <img
                                                                src={`/${service.icon}`}
                                                                alt={service.title}
                                                                className="w-full h-full object-cover"
                                                            />
                                                        ) : (
                                                            <Briefcase size={18} />
                                                        )}
                                                    </div>
                                                    <div className="min-w-0">
                                                        <p className="font-bold text-slate-900 group-hover:text-[#0a66c2] transition-colors truncate leading-tight text-sm">
                                                            {service.title}
                                                        </p>
                                                        <p className="text-xs text-slate-500 font-mono truncate leading-tight mt-0.5">
                                                            /{service.slug}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-5 py-3.5 text-slate-700 whitespace-nowrap">
                                                <span className="truncate max-w-[300px] block text-sm">
                                                    {service.subtitle || "N/A"}
                                                </span>
                                            </td>
                                            <td className="px-5 py-3.5 whitespace-nowrap">
                                                {service.is_campaign ? (
                                                    <span className="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-amber-50 text-amber-800 text-xs font-bold border border-amber-200">
                                                        <Zap size={12} /> Campaign
                                                    </span>
                                                ) : (
                                                    <span className="inline-flex items-center px-2 py-1 rounded-md bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200">
                                                        Standard
                                                    </span>
                                                )}
                                            </td>
                                            <td className="px-5 py-3.5 whitespace-nowrap">
                                                <span
                                                    className={`inline-flex items-center px-2 py-1 rounded-md text-xs font-bold ${
                                                        service.status
                                                            ? "bg-emerald-50 text-emerald-700 border border-emerald-200"
                                                            : "bg-slate-100 text-slate-700 border border-slate-200"
                                                    }`}
                                                >
                                                    {service.status ? "Active" : "Draft"}
                                                </span>
                                            </td>
                                            <td className="px-5 py-3.5 text-right whitespace-nowrap">
                                                <div className="flex items-center justify-end gap-2">
                                                    <Link
                                                        href={route("admin.campaigns.create", { service_id: service.id })}
                                                        className="w-8 h-8 flex items-center justify-center rounded-md text-amber-600 hover:bg-amber-50 border border-slate-200 hover:border-amber-200 transition-all"
                                                        title="Add Campaign"
                                                    >
                                                        <Zap size={15} />
                                                    </Link>
                                                    <Link
                                                        href={route("admin.services.edit", service.id)}
                                                        className="w-8 h-8 flex items-center justify-center rounded-md text-[#0a66c2] hover:bg-[#0a66c2]/10 border border-slate-200 hover:border-[#0a66c2]/20 transition-all"
                                                        title="Edit Service"
                                                    >
                                                        <Edit size={15} />
                                                    </Link>
                                                    <button
                                                        onClick={() => handleDelete(service.id)}
                                                        className="w-8 h-8 flex items-center justify-center rounded-md text-rose-600 hover:bg-rose-50 border border-slate-200 hover:border-rose-200 transition-all"
                                                        title="Delete Service"
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
                                            No services found matching your query.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
</div>
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
                                {services.from || 0} - {services.to || 0} of {services.total || 0}
                            </span>
                            <div className="flex items-center gap-1">
                                <button
                                    onClick={() => handlePageChange(services.prev_page_url)}
                                    disabled={!services.prev_page_url}
                                    className="w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
                                >
                                    <ChevronLeft size={16} />
                                </button>
                                <button
                                    onClick={() => handlePageChange(services.next_page_url)}
                                    disabled={!services.next_page_url}
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