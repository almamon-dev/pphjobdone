import React, { useState } from "react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Head, Link, router } from "@inertiajs/react";
import {
    FolderKanban,
    Search,
    Settings,
    Calendar,
    CheckCircle2,
    Clock,
    AlertCircle,
    ShoppingBag,
    Globe,
    ExternalLink,
    RefreshCw,
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
                <div className="bg-white rounded-md p-5 border border-slate-200/80 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-md bg-[#0a66c2]/10 text-[#0a66c2] flex items-center justify-center border border-[#0a66c2]/20">
                            <FolderKanban size={22} />
                        </div>
                        <div>
                            <h1 className="text-xl font-bold text-slate-900 leading-tight">
                                Client Bookings & Subscriptions
                            </h1>
                            <p className="text-sm text-slate-600 mt-0.5">
                                Track client orders, recurring Stripe subscriptions, auto-renewal settings, and project tasks.
                            </p>
                        </div>
                    </div>

                    <div className="text-sm font-semibold text-slate-700 bg-slate-100 px-3.5 py-1.5 rounded-md border border-slate-200">
                        Total Orders: <strong className="text-slate-900">{bookings.length}</strong>
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
                        <div className="w-full overflow-x-auto overflow-y-hidden touch-pan-x border border-slate-200/80 rounded-xl shadow-2xs mb-4">
                            <table className="w-full min-w-[850px] text-left border-collapse">
                                <thead>
                                    <tr className="border-b border-slate-200 bg-slate-50 text-xs font-bold text-slate-700">
                                        <th className="px-5 py-3 whitespace-nowrap">Client Profile & Order</th>
                                        <th className="px-5 py-3 whitespace-nowrap">Service / Package</th>
                                        <th className="px-5 py-3 whitespace-nowrap">Booking Date</th>
                                        <th className="px-5 py-3 whitespace-nowrap">Project Status</th>
                                        <th className="px-5 py-3 whitespace-nowrap">Payment & Auto-Renewal</th>
                                        <th className="px-5 py-3 text-right whitespace-nowrap">Actions</th>
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
                                                <td className="px-5 py-3.5 whitespace-nowrap">
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
                                                <td className="px-5 py-3.5 whitespace-nowrap">
                                                    <div>
                                                        <div className="flex items-center gap-2 leading-tight">
                                                            <p className="font-bold text-slate-900 text-sm">
                                                                {booking.service?.title || "Custom Service"}
                                                            </p>
                                                            <span className="text-xs font-extrabold text-[#0a66c2]">
                                                                ${Number(booking.price || 0).toFixed(2)}
                                                            </span>
                                                        </div>
                                                        <div className="flex items-center gap-2 mt-1 flex-wrap">
                                                            <span className="text-[11px] text-slate-600 font-medium bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200 shrink-0">
                                                                {booking.plan_name || "Standard Plan"}
                                                            </span>
                                                            {(() => {
                                                                const rawUrl = booking.website_url || booking.campaign_details?.website_url || booking.campaign_details?.links;
                                                                if (!rawUrl) return null;
                                                                const href = rawUrl.startsWith('http') ? rawUrl : `https://${rawUrl}`;

                                                                return (
                                                                    <a
                                                                        href={href}
                                                                        target="_blank"
                                                                        rel="noreferrer"
                                                                        className="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-[#0a66c2]/10 text-[#0a66c2] border border-[#0a66c2]/20 hover:bg-[#0a66c2] hover:text-white transition-all shadow-2xs shrink-0"
                                                                        title={`Open ${rawUrl}`}
                                                                    >
                                                                        <Globe size={11} />
                                                                        <span>Visit Website</span>
                                                                        <ExternalLink size={9} className="opacity-80" />
                                                                    </a>
                                                                );
                                                            })()}
                                                        </div>
                                                    </div>
                                                </td>

                                                {/* Column 3: Date */}
                                                <td className="px-5 py-3.5 text-slate-700 whitespace-nowrap">
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
                                                <td className="px-5 py-3.5 whitespace-nowrap">
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

                                                {/* Column 5: Payment & Auto-Renewal */}
                                                <td className="px-5 py-3.5 whitespace-nowrap">
                                                    <div className="space-y-1">
                                                        <span
                                                            className={`inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold border ${
                                                                booking.payment_status === "paid"
                                                                    ? "bg-emerald-50 text-emerald-700 border-emerald-200"
                                                                    : "bg-rose-50 text-rose-700 border-rose-200"
                                                            }`}
                                                        >
                                                            {booking.payment_status === "paid" ? "Paid" : "Unpaid"}
                                                        </span>

                                                        {booking.current_period_end && (
                                                            <div className="text-[11px] text-slate-500 font-medium">
                                                                Next Renewal: <strong className="text-slate-800">{new Date(booking.current_period_end).toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" })}</strong>
                                                            </div>
                                                        )}

                                                        {booking.cancel_at_period_end ? (
                                                            <span className="inline-block text-[10px] font-bold text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200">
                                                                Auto-Renew OFF
                                                            </span>
                                                        ) : booking.stripe_subscription_id ? (
                                                            <span className="inline-block text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
                                                                Auto-Renew ON
                                                            </span>
                                                        ) : null}
                                                    </div>
                                                </td>

                                                {/* Column 6: Actions */}
                                                <td className="px-5 py-3.5 text-right whitespace-nowrap">
                                                    <div className="flex items-center justify-end gap-2">
                                                        {booking.stripe_subscription_id && (
                                                            <button
                                                                onClick={() => {
                                                                    if (confirm(`Toggle auto-renewal for BKG-#${booking.id}?`)) {
                                                                        router.post(route("admin.bookings.toggle-auto-renew", booking.id), {}, { preserveScroll: true });
                                                                    }
                                                                }}
                                                                className={`px-2.5 py-1.5 rounded-md text-xs font-bold transition-all border ${
                                                                    booking.cancel_at_period_end
                                                                        ? "bg-emerald-50 text-emerald-700 border-emerald-300 hover:bg-emerald-100"
                                                                        : "bg-amber-50 text-amber-700 border-amber-300 hover:bg-amber-100"
                                                                }`}
                                                                title={booking.cancel_at_period_end ? "Turn Auto-Renewal ON" : "Turn Auto-Renewal OFF"}
                                                            >
                                                                {booking.cancel_at_period_end ? "Enable Auto-Renew" : "Disable Auto-Renew"}
                                                            </button>
                                                        )}

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
            </div>
        </AdminLayout>
    );
}