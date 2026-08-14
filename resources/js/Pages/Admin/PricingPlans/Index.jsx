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
    DollarSign,
    CircleDollarSign,
    Sparkles,
} from "lucide-react";

export default function Index({ pricing_plans, filters = {} }) {
    const [search, setSearch] = useState(filters.search || "");

    const handleSearch = (value) => {
        setSearch(value);
        updateFilters({ search: value, page: 1 });
    };

    const updateFilters = (newFilters) => {
        router.get(
            route("admin.pricing-plans.index"),
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
        if (confirm("Are you sure you want to delete this pricing plan?")) {
            router.delete(route("admin.pricing-plans.destroy", id));
        }
    };

    return (
        <AdminLayout>
            <Head title="Pricing Plans" />

            <div className="space-y-3 max-w-[1600px] mx-auto pb-8">
                {/* COMPACT TOP HEADER */}
                <div className="bg-white rounded-md p-3.5 border border-slate-200/80 shadow-2xs flex items-center justify-between">
                    <div className="flex items-center gap-2.5">
                        <div className="w-8 h-8 rounded-md bg-[#0a66c2]/10 text-[#0a66c2] flex items-center justify-center border border-[#0a66c2]/20 shrink-0">
                            <CircleDollarSign size={18} />
                        </div>
                        <div>
                            <h1 className="text-base font-bold text-slate-900 leading-tight">
                                Pricing Plans
                            </h1>
                            <p className="text-xs text-slate-500 mt-0.5">
                                Manage service packages, pricing tiers, and promotional features.
                            </p>
                        </div>
                    </div>

                    <Link
                        href={route("admin.pricing-plans.create")}
                        className="px-3 py-1.5 bg-[#0a66c2] hover:bg-[#084e96] text-white rounded-md text-xs font-semibold transition-all flex items-center gap-1 shadow-2xs"
                    >
                        <Plus size={15} /> Add Plan
                    </Link>
                </div>

                {/* MAIN TABLE CONTAINER */}
                <div className="bg-white rounded-md border border-slate-200/80 shadow-2xs overflow-hidden">
                    {/* SEARCH & FILTER BAR */}
                    <div className="p-3 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div className="relative w-full sm:w-[300px]">
                            <div className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <Search size={15} />
                            </div>
                            <input
                                type="text"
                                value={search}
                                onChange={(e) => handleSearch(e.target.value)}
                                placeholder="Search plans by name..."
                                className="w-full h-8 pl-9 pr-3 bg-white border border-slate-200 rounded-md text-xs font-medium focus:outline-none focus:border-[#0a66c2] focus:ring-1 focus:ring-[#0a66c2] transition-all text-slate-800 placeholder-slate-400"
                            />
                        </div>

                        <div className="text-xs text-slate-600 font-semibold self-end sm:self-auto">
                            Total Plans: <strong className="text-slate-900">{pricing_plans.total || 0}</strong>
                        </div>
                    </div>

                    {/* SUPER COMPACT TABLE */}
                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse">
                            <thead>
                                <tr className="border-b border-slate-200 bg-slate-50 text-[11px] font-bold text-slate-700">
                                    <th className="px-4 py-2">Plan Details</th>
                                    <th className="px-4 py-2">Price</th>
                                    <th className="px-4 py-2">Popular Tag</th>
                                    <th className="px-4 py-2">Status</th>
                                    <th className="px-4 py-2 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 text-xs">
                                {pricing_plans.data.length > 0 ? (
                                    pricing_plans.data.map((plan) => (
                                        <tr
                                            key={plan.id}
                                            className="hover:bg-slate-50/80 transition-colors group"
                                        >
                                            <td className="px-4 py-2">
                                                <div className="flex items-center gap-2.5">
                                                    <div className="w-7 h-7 rounded-md bg-[#0a66c2]/10 text-[#0a66c2] flex items-center justify-center border border-[#0a66c2]/20 shrink-0">
                                                        <DollarSign size={15} />
                                                    </div>
                                                    <div className="min-w-0">
                                                        <p className="font-bold text-slate-900 group-hover:text-[#0a66c2] transition-colors leading-tight text-xs">
                                                            {plan.name}
                                                        </p>
                                                        <div className="flex flex-wrap gap-1 mt-0.5">
                                                            {plan.services && plan.services.map((service) => (
                                                                <span
                                                                    key={service.id}
                                                                    className="text-[10px] bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded border border-slate-200 font-medium"
                                                                >
                                                                    {service.title}
                                                                </span>
                                                            ))}
                                                        </div>
                                                        {plan.subtitle && (
                                                            <p className="text-[10px] text-slate-500 truncate mt-0.5 leading-tight">
                                                                {plan.subtitle}
                                                            </p>
                                                        )}
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-4 py-2 font-bold text-slate-900 text-xs">
                                                ${plan.price}
                                            </td>
                                            <td className="px-4 py-2">
                                                {plan.is_popular ? (
                                                    <span className="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-purple-50 text-purple-700 text-[10px] font-bold border border-purple-200">
                                                        <Sparkles size={10} /> Popular
                                                    </span>
                                                ) : (
                                                    <span className="text-[11px] text-slate-400 font-medium">-</span>
                                                )}
                                            </td>
                                            <td className="px-4 py-2">
                                                <span
                                                    className={`inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-bold ${
                                                        plan.status
                                                            ? "bg-emerald-50 text-emerald-700 border border-emerald-200"
                                                            : "bg-slate-100 text-slate-700 border border-slate-200"
                                                    }`}
                                                >
                                                    {plan.status ? "Active" : "Draft"}
                                                </span>
                                            </td>
                                            <td className="px-4 py-2 text-right">
                                                <div className="flex items-center justify-end gap-1.5">
                                                    <Link
                                                        href={route("admin.pricing-plans.edit", plan.id)}
                                                        className="w-7 h-7 flex items-center justify-center rounded-md text-[#0a66c2] hover:bg-[#0a66c2]/10 border border-slate-200 hover:border-[#0a66c2]/20 transition-all"
                                                        title="Edit Plan"
                                                    >
                                                        <Edit size={14} />
                                                    </Link>
                                                    <button
                                                        onClick={() => handleDelete(plan.id)}
                                                        className="w-7 h-7 flex items-center justify-center rounded-md text-rose-600 hover:bg-rose-50 border border-slate-200 hover:border-rose-200 transition-all"
                                                        title="Delete Plan"
                                                    >
                                                        <Trash2 size={14} />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="5" className="px-4 py-10 text-center text-slate-400 text-xs">
                                            No pricing plans found matching your query.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* COMPACT PAGINATION FOOTER */}
                    <div className="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 py-2 border-t border-slate-200 bg-slate-50/50">
                        <div className="flex items-center gap-2 text-xs text-slate-600">
                            <span>Rows per page:</span>
                            <div className="relative">
                                <select
                                    value={filters.per_page || 10}
                                    onChange={handlePerPageChange}
                                    className="h-8 pl-2.5 pr-7 bg-white border border-slate-200 rounded-md text-xs font-semibold text-slate-800 appearance-none cursor-pointer focus:border-[#0a66c2] outline-none"
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
                                {pricing_plans.from || 0} - {pricing_plans.to || 0} of {pricing_plans.total || 0}
                            </span>
                            <div className="flex items-center gap-1">
                                <button
                                    onClick={() => handlePageChange(pricing_plans.prev_page_url)}
                                    disabled={!pricing_plans.prev_page_url}
                                    className="w-7 h-7 flex items-center justify-center rounded-md border border-slate-200 hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
                                >
                                    <ChevronLeft size={14} />
                                </button>
                                <button
                                    onClick={() => handlePageChange(pricing_plans.next_page_url)}
                                    disabled={!pricing_plans.next_page_url}
                                    className="w-7 h-7 flex items-center justify-center rounded-md border border-slate-200 hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
                                >
                                    <ChevronRight size={14} />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
