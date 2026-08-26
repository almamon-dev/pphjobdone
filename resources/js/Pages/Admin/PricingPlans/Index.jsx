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
    RefreshCw,
    CheckCircle,
    AlertCircle,
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

    const handleResync = (id) => {
        router.post(route("admin.pricing-plans.resync", id), {}, { preserveScroll: true });
    };

    return (
        <AdminLayout>
            <Head title="Pricing Plans" />

            <div className="space-y-3 max-w-[1600px] mx-auto pb-8">
                {/* COMPACT TOP HEADER */}
                <div className="bg-white rounded-md p-3.5 border border-slate-200/80 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div className="flex items-center gap-2.5">
                        <div className="w-8 h-8 rounded-md bg-[#0a66c2]/10 text-[#0a66c2] flex items-center justify-center border border-[#0a66c2]/20 shrink-0">
                            <CircleDollarSign size={18} />
                        </div>
                        <div>
                            <h1 className="text-base font-bold text-slate-900 leading-tight">
                                Pricing Plans & Stripe Products
                            </h1>
                            <p className="text-xs text-slate-500 mt-0.5">
                                Manage service packages, recurring billing rates, and Stripe product auto-sync.
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
                    <div className="overflow-x-auto p-2">
                        <table className="w-full min-w-[900px] text-left border-collapse">
                            <thead>
                                <tr className="border-b border-slate-200 bg-slate-50 text-[11px] font-bold text-slate-700">
                                    <th className="px-4 py-2.5 whitespace-nowrap">Plan Details</th>
                                    <th className="px-4 py-2.5 whitespace-nowrap">Price & Interval</th>
                                    <th className="px-4 py-2.5 whitespace-nowrap">Stripe Product & Price Sync</th>
                                    <th className="px-4 py-2.5 whitespace-nowrap">Status</th>
                                    <th className="px-4 py-2.5 text-right whitespace-nowrap">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 text-xs">
                                {pricing_plans.data.length > 0 ? (
                                    pricing_plans.data.map((plan) => (
                                        <tr
                                            key={plan.id}
                                            className="hover:bg-slate-50/80 transition-colors group"
                                        >
                                            <td className="px-4 py-2.5 whitespace-nowrap">
                                                <div className="flex items-center gap-2.5">
                                                    <div className="w-7 h-7 rounded-md bg-[#0a66c2]/10 text-[#0a66c2] flex items-center justify-center border border-[#0a66c2]/20 shrink-0">
                                                        <DollarSign size={15} />
                                                    </div>
                                                    <div className="min-w-0">
                                                        <div className="flex items-center gap-1.5">
                                                            <p className="font-bold text-slate-900 group-hover:text-[#0a66c2] transition-colors leading-tight text-xs">
                                                                {plan.name}
                                                            </p>
                                                            {plan.is_popular && (
                                                                <span className="inline-flex items-center gap-0.5 px-1.5 py-0.2 rounded bg-purple-50 text-purple-700 text-[9px] font-bold border border-purple-200">
                                                                    <Sparkles size={9} /> Popular
                                                                </span>
                                                            )}
                                                        </div>
                                                        <div className="flex flex-wrap gap-1 mt-0.5">
                                                            {plan.services && plan.services.map((service) => (
                                                                <span
                                                                    key={service.id}
                                                                    className="text-[10px] bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded border border-slate-200 font-medium capitalize"
                                                                >
                                                                    {service.title ? service.title.toLowerCase().replace(/\b\w/g, (c) => c.toUpperCase()) : ''}
                                                                </span>
                                                            ))}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-4 py-2.5 font-bold text-slate-900 text-xs whitespace-nowrap">
                                                ${plan.price} <span className="text-[11px] text-slate-500 font-normal">/ {plan.billing_interval === 'year' ? 'year' : 'month'}</span>
                                            </td>
                                            <td className="px-4 py-2.5 whitespace-nowrap">
                                                {plan.stripe_product_id && plan.stripe_price_id ? (
                                                    <div className="space-y-0.5">
                                                        <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 text-[10px] font-bold border border-emerald-200">
                                                            <CheckCircle size={10} className="text-emerald-600" /> Stripe Synced
                                                        </span>
                                                        <p className="text-[10px] font-mono text-slate-400">
                                                            Prod: {plan.stripe_product_id}
                                                        </p>
                                                    </div>
                                                ) : (
                                                    <div className="flex items-center gap-1.5">
                                                        <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 text-[10px] font-bold border border-amber-200">
                                                            <AlertCircle size={10} className="text-amber-600" /> Not Synced
                                                        </span>
                                                        <button
                                                            onClick={() => handleResync(plan.id)}
                                                            className="p-1 text-[#0a66c2] hover:bg-[#0a66c2]/10 rounded border border-slate-200 text-[10px] font-semibold flex items-center gap-1"
                                                            title="Sync Product with Stripe"
                                                        >
                                                            <RefreshCw size={10} /> Sync
                                                        </button>
                                                    </div>
                                                )}
                                            </td>
                                            <td className="px-4 py-2.5 whitespace-nowrap">
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
                                            <td className="px-4 py-2.5 text-right whitespace-nowrap">
                                                <div className="flex items-center justify-end gap-1.5">
                                                    <button
                                                        onClick={() => handleResync(plan.id)}
                                                        className="w-7 h-7 flex items-center justify-center rounded-md text-slate-600 hover:bg-slate-100 border border-slate-200 transition-all"
                                                        title="Re-sync with Stripe"
                                                    >
                                                        <RefreshCw size={13} />
                                                    </button>
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
                                        <td colSpan="5" className="px-4 py-8 text-center text-slate-400 text-xs">
                                            No pricing plans found.
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