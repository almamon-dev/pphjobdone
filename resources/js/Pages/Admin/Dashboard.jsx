import AdminLayout from "@/Layouts/AdminLayout";
import { Head, Link, router } from "@inertiajs/react";
import React, { useState } from "react";
import {
    Users,
    Briefcase,
    FolderKanban,
    DollarSign,
    Clock,
    TrendingUp,
    TrendingDown,
    Mail,
    ChevronRight,
    ChevronDown,
    ExternalLink,
    Zap,
} from "lucide-react";

export default function Dashboard({
    auth,
    stats = {},
    filters = {},
    recentBookings = [],
    recentContacts = [],
    recentUsers = [],
    lineChartData = [],
    barChartData = [],
}) {
    const user = auth.user;
    const [hoveredPoint, setHoveredPoint] = useState(null);

    const handlePeriodChange = (period) => {
        router.get(
            route("dashboard"),
            { ...filters, period },
            { preserveState: true, replace: true }
        );
    };

    // 5 STAT CARDS (FULLY DYNAMIC FROM MYSQL DATABASE)
    const cardList = [
        {
            title: "Total Revenue",
            value: `$${Number(stats.revenue || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`,
            trend: `${stats.revenueGrowth >= 0 ? "+" : ""}${stats.revenueGrowth || 0}%`,
            trendLabel: "from last month",
            trendType: (stats.revenueGrowth || 0) >= 0 ? "up" : "down",
            icon: DollarSign,
            iconBg: "bg-emerald-100/70",
            iconColor: "text-emerald-600",
        },
        {
            title: "Total Bookings",
            value: Number(stats.bookings || 0).toLocaleString(),
            trend: `${stats.bookingsGrowth >= 0 ? "+" : ""}${stats.bookingsGrowth || 0}%`,
            trendLabel: "from last month",
            trendType: (stats.bookingsGrowth || 0) >= 0 ? "up" : "down",
            icon: FolderKanban,
            iconBg: "bg-blue-100/70",
            iconColor: "text-blue-600",
        },
        {
            title: "Active Services",
            value: Number(stats.services || 0).toLocaleString(),
            trend: `${stats.services || 0} active`,
            trendLabel: "in platform",
            trendType: "up",
            icon: Briefcase,
            iconBg: "bg-indigo-100/70",
            iconColor: "text-indigo-600",
        },
        {
            title: "Success Rate",
            value: `${stats.successRate || 100}%`,
            trend: "100%",
            trendLabel: "fulfillment rate",
            trendType: "up",
            icon: TrendingUp,
            iconBg: "bg-purple-100/70",
            iconColor: "text-purple-600",
        },
        {
            title: "Total Users",
            value: Number(stats.users || 0).toLocaleString(),
            trend: `${stats.users || 0} users`,
            trendLabel: "registered accounts",
            trendType: "up",
            icon: Users,
            iconBg: "bg-amber-100/70",
            iconColor: "text-amber-600",
        },
    ];

    // DYNAMIC LINE CHART CALCULATIONS
    const lineChartDays = lineChartData.length > 0 ? lineChartData : [
        { label: "Mon", val: 0 },
        { label: "Tue", val: 0 },
        { label: "Wed", val: 0 },
        { label: "Thu", val: 0 },
        { label: "Fri", val: 0 },
        { label: "Sat", val: 0 },
        { label: "Sun", val: 0 },
    ];

    const maxLineVal = Math.max(...lineChartDays.map((d) => d.val), 5);
    const svgWidth = 600;
    const svgHeight = 220;
    const paddingX = 12;
    const paddingY = 25;

    const linePoints = lineChartDays.map((d, index) => {
        const x = paddingX + (index / Math.max(lineChartDays.length - 1, 1)) * (svgWidth - 2 * paddingX);
        const y = svgHeight - paddingY - (d.val / maxLineVal) * (svgHeight - 2 * paddingY);
        return { ...d, x, y, index };
    });

    const smoothLinePath = linePoints.reduce((acc, p, i) => {
        if (i === 0) return `M ${p.x} ${p.y}`;
        const prev = linePoints[i - 1];
        const cx1 = prev.x + (p.x - prev.x) / 2;
        const cy1 = prev.y;
        const cx2 = prev.x + (p.x - prev.x) / 2;
        const cy2 = p.y;
        return `${acc} C ${cx1} ${cy1}, ${cx2} ${cy2}, ${p.x} ${p.y}`;
    }, "");

    // DYNAMIC BAR CHART CALCULATIONS
    const barChartWeeks = barChartData.length > 0 ? barChartData : [
        { label: "Week 1", val: 0 },
        { label: "Week 2", val: 0 },
        { label: "Week 3", val: 0 },
        { label: "Week 4", val: 0 },
        { label: "Week 5", val: 0 },
    ];
    const maxBarVal = Math.max(...barChartWeeks.map((b) => b.val), 5);

    return (
        <AdminLayout>
            <Head title="Dashboard Overview" />

            <div className="space-y-4 max-w-[1600px] mx-auto pb-12">
                {/* PAGE HEADER */}
                <div className="bg-white rounded-md p-5 border border-slate-200/80 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h1 className="text-xl font-bold text-slate-900 leading-tight">
                            Welcome back, {user?.name || "Admin"}
                        </h1>
                        <p className="text-sm text-slate-600 mt-0.5">
                            Here is real-time performance analytics and dynamic system activity.
                        </p>
                    </div>

                    <div className="text-xs font-semibold text-slate-600 bg-slate-100 px-3 py-1.5 rounded-md border border-slate-200">
                        Today: <strong className="text-slate-900">{new Date().toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" })}</strong>
                    </div>
                </div>

                {/* TOP 5 STAT CARDS ROW */}
                <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                    {cardList.map((card, idx) => {
                        const Icon = card.icon;
                        return (
                            <div
                                key={idx}
                                className="bg-white p-4 rounded-md border border-slate-200/80 shadow-2xs hover:shadow-xs transition-all flex flex-col justify-between h-[120px]"
                            >
                                <div className="flex items-center justify-between">
                                    <span className="text-xs font-semibold text-slate-600">{card.title}</span>
                                    <div className={`w-8 h-8 rounded-md ${card.iconBg} ${card.iconColor} flex items-center justify-center border border-slate-200/50`}>
                                        <Icon size={16} />
                                    </div>
                                </div>

                                <div>
                                    <div className="text-xl font-bold text-slate-900 tracking-tight">
                                        {card.value}
                                    </div>
                                    <div className="text-[11px] mt-0.5 flex items-center gap-1">
                                        <span
                                            className={`font-bold ${
                                                card.trendType === "up" ? "text-emerald-600" : "text-rose-600"
                                            }`}
                                        >
                                            {card.trend}
                                        </span>
                                        <span className="text-slate-500 font-medium">{card.trendLabel}</span>
                                    </div>
                                </div>
                            </div>
                        );
                    })}
                </div>

                {/* DYNAMIC CHARTS ROW */}
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-4">
                    {/* LEFT CHART: Filterable Booking Volume */}
                    <div className="lg:col-span-7 bg-white rounded-md border border-slate-200/80 p-5 shadow-2xs">
                        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                            <div>
                                <h3 className="text-sm font-bold text-slate-900">
                                    Booking Volume Trend
                                </h3>
                                <p className="text-xs text-slate-500">Filter real-time order creation activity</p>
                            </div>

                            {/* FILTER DROPDOWN */}
                            <div className="relative">
                                <select
                                    value={filters.period || "7_days"}
                                    onChange={(e) => handlePeriodChange(e.target.value)}
                                    className="h-8 pl-3 pr-8 text-xs bg-white border border-slate-200 rounded-md font-semibold text-slate-800 appearance-none cursor-pointer focus:border-[#0a66c2] outline-none shadow-2xs"
                                >
                                    <option value="7_days">Last 7 Days</option>
                                    <option value="30_days">Last 30 Days</option>
                                    <option value="12_months">Last 12 Months</option>
                                </select>
                                <div className="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                    <ChevronDown size={14} />
                                </div>
                            </div>
                        </div>

                        <div className="relative w-full h-[220px]">
                            {/* Y-Axis Label Values */}
                            <div className="absolute left-0 top-0 bottom-8 flex flex-col justify-between text-[10px] text-slate-400 font-semibold pointer-events-none">
                                <span>{maxLineVal}</span>
                                <span>{Math.round(maxLineVal * 0.75)}</span>
                                <span>{Math.round(maxLineVal * 0.5)}</span>
                                <span>{Math.round(maxLineVal * 0.25)}</span>
                                <span>0</span>
                            </div>

                            {/* SVG Gradient Curved Line Chart */}
                            <div className="ml-6 mr-1 h-full">
                                <svg
                                    viewBox={`0 0 ${svgWidth} ${svgHeight}`}
                                    preserveAspectRatio="none"
                                    className="w-full h-full overflow-visible"
                                >
                                    <defs>
                                        <linearGradient id="blueGradientArea" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%" stopColor="#0a66c2" stopOpacity="0.22" />
                                            <stop offset="100%" stopColor="#0a66c2" stopOpacity="0.0" />
                                        </linearGradient>
                                    </defs>

                                    {/* Horizontal Dashed Lines */}
                                    {[0, 0.25, 0.5, 0.75, 1].map((r, i) => {
                                        const yLine = paddingY + r * (svgHeight - 2 * paddingY);
                                        return (
                                            <line
                                                key={i}
                                                x1={paddingX}
                                                y1={yLine}
                                                x2={svgWidth - paddingX}
                                                y2={yLine}
                                                stroke="#f1f5f9"
                                                strokeWidth="1.5"
                                                strokeDasharray="4 4"
                                            />
                                        );
                                    })}

                                    {/* Gradient Area Fill under curve */}
                                    {smoothLinePath && linePoints.length > 0 && (
                                        <path
                                            d={`${smoothLinePath} L ${linePoints[linePoints.length - 1].x} ${svgHeight - paddingY} L ${linePoints[0].x} ${svgHeight - paddingY} Z`}
                                            fill="url(#blueGradientArea)"
                                        />
                                    )}

                                    {/* Smooth Blue Line */}
                                    {smoothLinePath && (
                                        <path
                                            d={smoothLinePath}
                                            fill="none"
                                            stroke="#0a66c2"
                                            strokeWidth="3"
                                            strokeLinecap="round"
                                        />
                                    )}

                                    {/* Dots and Labels */}
                                    {linePoints.map((pt) => (
                                        <g key={pt.index}>
                                            <circle
                                                cx={pt.x}
                                                cy={pt.y}
                                                r={hoveredPoint === pt.index ? "6.5" : "4.5"}
                                                fill="#ffffff"
                                                stroke="#0a66c2"
                                                strokeWidth="2.5"
                                                className="transition-all cursor-pointer shadow-sm"
                                                onMouseEnter={() => setHoveredPoint(pt.index)}
                                                onMouseLeave={() => setHoveredPoint(null)}
                                            />
                                            <text
                                                x={pt.x}
                                                y={svgHeight - 2}
                                                textAnchor="middle"
                                                fill="#64748b"
                                                fontSize="11"
                                                fontWeight="600"
                                            >
                                                {pt.label}
                                            </text>
                                        </g>
                                    ))}
                                </svg>

                                {/* Tooltip */}
                                {hoveredPoint !== null && (
                                    <div
                                        style={{
                                            left: `${(linePoints[hoveredPoint].x / svgWidth) * 100}%`,
                                            top: `${(linePoints[hoveredPoint].y / svgHeight) * 100 - 15}%`,
                                        }}
                                        className="absolute -translate-x-1/2 -translate-y-full bg-slate-900 text-white text-[11px] font-bold px-2.5 py-1 rounded-md shadow-md pointer-events-none z-20 whitespace-nowrap"
                                    >
                                        {linePoints[hoveredPoint].label}: {linePoints[hoveredPoint].val} Bookings
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* RIGHT CHART: Sleek Gradient Bar Chart */}
                    <div className="lg:col-span-5 bg-white rounded-md border border-slate-200/80 p-5 shadow-2xs flex flex-col justify-between">
                        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                            <div>
                                <h3 className="text-sm font-bold text-slate-900">
                                    Weekly Booking Rate (Last 5 Weeks)
                                </h3>
                                <p className="text-xs text-slate-500">Weekly order completion volume</p>
                            </div>
                            <span className="text-xs font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">
                                MySQL Query
                            </span>
                        </div>

                        <div className="relative w-full h-[220px] flex">
                            <div className="w-8 flex flex-col justify-between text-[10px] text-slate-400 font-semibold pb-8 pointer-events-none">
                                <span>{maxBarVal}</span>
                                <span>{Math.round(maxBarVal * 0.75)}</span>
                                <span>{Math.round(maxBarVal * 0.5)}</span>
                                <span>{Math.round(maxBarVal * 0.25)}</span>
                                <span>0</span>
                            </div>

                            <div className="flex-1 flex items-end justify-between gap-3 pb-8 px-2 h-full">
                                {barChartWeeks.map((bar, idx) => {
                                    const barHeightPercent = maxBarVal > 0 ? Math.round((bar.val / maxBarVal) * 100) : 0;
                                    return (
                                        <div key={idx} className="flex-1 flex flex-col items-center gap-2 h-full justify-end group">
                                            <div className="w-full flex items-end justify-center h-full relative">
                                                <div
                                                    style={{ height: `${Math.max(barHeightPercent, 8)}%` }}
                                                    className="w-full max-w-[44px] bg-gradient-to-t from-slate-100 via-[#0a66c2]/30 to-[#0a66c2] group-hover:to-[#084e96] rounded-t-md transition-all relative shadow-2xs"
                                                >
                                                    <div className="opacity-0 group-hover:opacity-100 absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-bold px-1.5 py-0.5 rounded pointer-events-none transition-opacity whitespace-nowrap z-10 shadow-md">
                                                        {bar.val}
                                                    </div>
                                                </div>
                                            </div>
                                            <span className="text-xs text-slate-600 font-semibold whitespace-nowrap">
                                                {bar.label}
                                            </span>
                                        </div>
                                    );
                                })}
                            </div>
                        </div>
                    </div>
                </div>

                {/* BOTTOM DYNAMIC TABLES */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    {/* RECENT BOOKINGS TABLE */}
                    <div className="bg-white rounded-md border border-slate-200/80 shadow-2xs overflow-hidden">
                        <div className="p-4 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div className="flex items-center gap-2">
                                <FolderKanban size={17} className="text-[#0a66c2]" />
                                <h3 className="text-sm font-bold text-slate-900">Recent Client Bookings</h3>
                            </div>
                            <Link
                                href={route("admin.bookings.index")}
                                className="text-xs text-[#0a66c2] font-bold hover:underline flex items-center gap-1"
                            >
                                View All <ChevronRight size={14} />
                            </Link>
                        </div>

                        <div className="overflow-x-auto">
<div className="w-full overflow-x-auto overflow-y-hidden touch-pan-x border border-slate-200/80 rounded-xl shadow-2xs mb-4">
                            <table className="w-full min-w-[850px] text-left border-collapse">
                                <thead>
                                    <tr className="border-b border-slate-200 bg-slate-50 text-[11px] font-bold text-slate-700">
                                        <th className="px-4 py-2 whitespace-nowrap">Client</th>
                                        <th className="px-4 py-2 whitespace-nowrap">Service / Plan</th>
                                        <th className="px-4 py-2 whitespace-nowrap">Price</th>
                                        <th className="px-4 py-2 whitespace-nowrap">Payment</th>
                                        <th className="px-4 py-2 text-right whitespace-nowrap">Action</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100 text-xs">
                                    {recentBookings.length > 0 ? (
                                        recentBookings.map((b) => (
                                            <tr key={b.id} className="hover:bg-slate-50/80 transition-colors">
                                                <td className="px-4 py-2.5 whitespace-nowrap">
                                                    <div className="font-bold text-slate-900">
                                                        {b.user?.name || "Client #" + b.user_id}
                                                    </div>
                                                    {b.user?.email && (
                                                        <div className="text-[11px] text-slate-500 font-medium truncate max-w-[140px]">
                                                            {b.user.email}
                                                        </div>
                                                    )}
                                                </td>
                                                <td className="px-4 py-2.5 text-slate-800 font-medium whitespace-nowrap">
                                                    {b.service?.title || b.plan_name || "Custom Service"}
                                                </td>
                                                <td className="px-4 py-2.5 font-bold text-slate-900 whitespace-nowrap">
                                                    ${Number(b.price || 0).toFixed(2)}
                                                </td>
                                                <td className="px-4 py-2.5 whitespace-nowrap">
                                                     <span
                                                        className={`inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold border capitalize ${
                                                            b.payment_status === "paid" || b.payment_status === "succeeded"
                                                                ? "bg-emerald-50 text-emerald-700 border-emerald-200"
                                                                : b.payment_status === "pending"
                                                                ? "bg-amber-50 text-amber-700 border-amber-200"
                                                                : "bg-rose-50 text-rose-700 border-rose-200"
                                                        }`}
                                                    >
                                                        {b.payment_status || "Pending"}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-2.5 text-right whitespace-nowrap">
                                                    <Link
                                                        href={route("admin.bookings.index")}
                                                        className="w-7 h-7 inline-flex items-center justify-center rounded-md text-[#0a66c2] hover:bg-[#0a66c2]/10 border border-slate-200 transition-all"
                                                        title="Manage"
                                                    >
                                                        <ExternalLink size={14} />
                                                    </Link>
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="5" className="p-8 text-center text-slate-400 text-xs">
                                                No recent bookings recorded.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
</div>
                        </div>
                    </div>

                    {/* RECENT CONTACT MESSAGES TABLE */}
                    <div className="bg-white rounded-md border border-slate-200/80 shadow-2xs overflow-hidden">
                        <div className="p-4 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div className="flex items-center gap-2">
                                <Mail size={17} className="text-[#0a66c2]" />
                                <h3 className="text-sm font-bold text-slate-900">Recent Inquiries & Messages</h3>
                            </div>
                            <Link
                                href={route("admin.contacts.index")}
                                className="text-xs text-[#0a66c2] font-bold hover:underline flex items-center gap-1"
                            >
                                View All <ChevronRight size={14} />
                            </Link>
                        </div>

                        <div className="overflow-x-auto">
<div className="w-full overflow-x-auto overflow-y-hidden touch-pan-x border border-slate-200/80 rounded-xl shadow-2xs mb-4">
                            <table className="w-full min-w-[850px] text-left border-collapse">
                                <thead>
                                    <tr className="border-b border-slate-200 bg-slate-50 text-[11px] font-bold text-slate-700">
                                        <th className="px-4 py-2 whitespace-nowrap">Sender</th>
                                        <th className="px-4 py-2 whitespace-nowrap">Email</th>
                                        <th className="px-4 py-2 whitespace-nowrap">Message Snippet</th>
                                        <th className="px-4 py-2 text-right whitespace-nowrap">Action</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100 text-xs">
                                    {recentContacts.length > 0 ? (
                                        recentContacts.map((c) => (
                                            <tr key={c.id} className="hover:bg-slate-50/80 transition-colors">
                                                <td className="px-4 py-2.5 font-bold text-slate-900 truncate max-w-[120px] whitespace-nowrap">
                                                    {c.first_name ? `${c.first_name} ${c.last_name || ""}` : "Contact"}
                                                </td>
                                                <td className="px-4 py-2.5 text-slate-600 font-medium truncate max-w-[140px] whitespace-nowrap">
                                                    {c.email}
                                                </td>
                                                <td className="px-4 py-2.5 text-slate-800 truncate max-w-[180px] whitespace-nowrap">
                                                    {c.message || c.subject || "Inquiry"}
                                                </td>
                                                <td className="px-4 py-2.5 text-right whitespace-nowrap">
                                                    <Link
                                                        href={route("admin.contacts.index")}
                                                        className="w-7 h-7 inline-flex items-center justify-center rounded-md text-[#0a66c2] hover:bg-[#0a66c2]/10 border border-slate-200 transition-all"
                                                        title="View"
                                                    >
                                                        <ExternalLink size={14} />
                                                    </Link>
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="4" className="p-8 text-center text-slate-400 text-xs">
                                                No recent contact messages.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
</div>
                        </div>
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}