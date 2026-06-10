import React from "react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Head, Link } from "@inertiajs/react";
import {
    Home,
    FolderKanban,
    ChevronRight,
    Search,
    CreditCard,
    Settings
} from "lucide-react";

export default function BookingsIndex({ bookings }) {
    return (
        <AdminLayout>
            <Head title="Client Bookings" />

            <div className="space-y-6 max-w-full mx-auto pb-20">
                {/* Top Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <h1 className="text-[24px] font-bold text-[#2f3344] tracking-tight">
                            Client Bookings
                        </h1>
                        <div className="flex items-center gap-2 text-[13px] text-[#727586] mt-1">
                            <Home size={16} className="text-[#727586]" />
                            <span className="text-[#c3c4ca]">-</span>
                            <span>Track client tasks & progress</span>
                        </div>
                    </div>
                </div>

                {/* Main Content Card */}
                <div className="bg-white rounded-[12px] border border-[#e3e4e8] shadow-sm overflow-hidden">
                    {/* Table Area */}
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-[#e3e4e8]">
                                    <th className="text-left px-5 py-3 text-[12px] font-bold text-[#2f3344] uppercase tracking-wider bg-[#fafbfc]">
                                        Client
                                    </th>
                                    <th className="text-left px-4 py-3 text-[12px] font-bold text-[#2f3344] uppercase tracking-wider bg-[#fafbfc]">
                                        Plan / Service
                                    </th>
                                    <th className="text-left px-4 py-3 text-[12px] font-bold text-[#2f3344] uppercase tracking-wider bg-[#fafbfc]">
                                        Date
                                    </th>
                                    <th className="text-left px-4 py-3 text-[12px] font-bold text-[#2f3344] uppercase tracking-wider bg-[#fafbfc]">
                                        Booking Status
                                    </th>
                                    <th className="text-left px-4 py-3 text-[12px] font-bold text-[#2f3344] uppercase tracking-wider bg-[#fafbfc]">
                                        Payment
                                    </th>
                                    <th className="px-5 py-3 text-right bg-[#fafbfc]">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[#f1f2f4]">
                                {bookings.length > 0 ? (
                                    bookings.map((booking) => (
                                        <tr
                                            key={booking.id}
                                            className="hover:bg-[#fafbfc] transition-colors group"
                                        >
                                            <td className="px-5 py-3">
                                                <div className="flex items-center gap-3">
                                                    <div className="w-8 h-8 rounded-md bg-[#f4f0ff] flex items-center justify-center text-[#673ab7] border border-[#e9e3ff]">
                                                        <FolderKanban size={16} />
                                                    </div>
                                                    <div>
                                                        <p className="text-[13px] font-bold text-[#2f3344] group-hover:text-[#673ab7] transition-colors leading-tight">
                                                            {booking.user?.name || 'Unknown'}
                                                        </p>
                                                        <p className="text-[11px] text-[#727586] font-medium mt-0.5 line-clamp-1 max-w-[200px]">
                                                            BKG-{booking.id}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-4 py-3">
                                                <div>
                                                    <p className="text-[13px] font-bold text-[#2f3344] leading-tight">
                                                        {booking.service?.title || 'SEO / Custom'}
                                                    </p>
                                                    <p className="text-[11px] text-[#727586] font-medium mt-0.5">
                                                        {booking.plan_name}
                                                    </p>
                                                </div>
                                            </td>
                                            <td className="px-4 py-3">
                                                <div className="text-[12px] font-medium text-[#2f3344]">
                                                    {new Date(booking.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                                                </div>
                                            </td>
                                            <td className="px-4 py-3">
                                                <span
                                                    className={`inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border capitalize ${
                                                        booking.status === 'active' || booking.status === 'ongoing'
                                                            ? "bg-[#f4f0ff] text-[#673ab7] border-[#e9e3ff]"
                                                            : booking.status === 'completed'
                                                            ? "bg-emerald-50 text-emerald-600 border-emerald-100"
                                                            : "bg-amber-50 text-amber-600 border-amber-100"
                                                    }`}
                                                >
                                                    {booking.status}
                                                </span>
                                            </td>
                                            <td className="px-4 py-3">
                                                <span
                                                    className={`inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border capitalize ${
                                                        booking.payment_status === 'paid'
                                                            ? "bg-green-50 text-green-600 border-green-100"
                                                            : "bg-red-50 text-red-600 border-red-100"
                                                    }`}
                                                >
                                                    {booking.payment_status === 'paid' ? 'Paid' : 'Unpaid'}
                                                </span>
                                            </td>
                                            <td className="px-5 py-3 text-right">
                                                <div className="flex items-center justify-end gap-2">
                                                    <Link
                                                        href={`/admin/bookings/${booking.id}`}
                                                        className="h-[28px] px-3 flex items-center justify-center rounded-[6px] text-[#673ab7] bg-[#f4f0ff] hover:bg-[#673ab7] hover:text-white transition-all shadow-sm border border-transparent font-bold text-[11px]"
                                                        title="Manage Tasks"
                                                    >
                                                        <Settings size={14} className="mr-1.5" />
                                                        Manage
                                                    </Link>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="5" className="px-7 py-24 text-center">
                                            <div className="flex flex-col items-center gap-3 text-[#727586]">
                                                <div className="w-16 h-16 bg-[#f8f9fa] rounded-full flex items-center justify-center mb-2">
                                                    <FolderKanban size={30} className="text-[#c3c4ca]" />
                                                </div>
                                                <p className="text-[16px] font-bold text-[#2f3344]">No bookings found</p>
                                                <p className="text-[14px]">There are currently no active client bookings.</p>
                                            </div>
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
