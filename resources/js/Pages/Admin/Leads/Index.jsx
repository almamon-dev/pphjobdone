import React, { useState } from "react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Head, Link, router } from "@inertiajs/react";
import {
    Search,
    Trash2,
    MessageSquare,
    Mail,
    Phone,
    User as UserIcon,
    Building,
    Flame,
    Zap,
    Snowflake,
    X,
    Bot,
    UserCheck,
    DollarSign,
    CheckCircle2,
    ExternalLink,
    ChevronDown
} from "lucide-react";

export default function Index({ leads, filters = {}, stats = {} }) {
    const [search, setSearch] = useState(filters.search || "");
    const [qualificationFilter, setQualificationFilter] = useState(filters.qualification || "all");
    const [selectedChatLead, setSelectedChatLead] = useState(null);

    const getStatusSelectStyle = (status) => {
        switch (status) {
            case "contacted":
                return "bg-amber-50 text-amber-800 border-amber-300 focus:ring-amber-500/20";
            case "converted":
                return "bg-emerald-50 text-emerald-800 border-emerald-300 focus:ring-emerald-500/20";
            case "closed":
                return "bg-slate-100 text-slate-700 border-slate-300 focus:ring-slate-500/20";
            default: // new
                return "bg-blue-50 text-blue-700 border-blue-300 focus:ring-blue-500/20";
        }
    };


    const handleSearch = (e) => {
        e.preventDefault();
        updateFilters({ search, qualification: qualificationFilter === "all" ? "" : qualificationFilter });
    };

    const handleQualificationChange = (status) => {
        setQualificationFilter(status);
        updateFilters({ qualification: status === "all" ? "" : status, page: 1 });
    };

    const updateFilters = (newFilters) => {
        router.get(
            route("admin.leads.index"),
            { ...filters, ...newFilters },
            { preserveState: true, replace: true }
        );
    };

    const handleStatusChange = (leadId, newStatus) => {
        router.patch(route("admin.leads.status", leadId), { status: newStatus }, { preserveScroll: true });
    };

    const handleDelete = (id) => {
        if (confirm("Are you sure you want to delete this AI lead?")) {
            router.delete(route("admin.leads.destroy", id), { preserveScroll: true });
        }
    };

    const getQualificationBadge = (status) => {
        switch (status) {
            case "Hot":
                return (
                    <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700 border border-rose-200 shadow-2xs">
                        <Flame size={13} className="text-rose-600 fill-rose-500" /> Hot Lead
                    </span>
                );
            case "Warm":
                return (
                    <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                        <Zap size={13} className="text-amber-600 fill-amber-500" /> Warm Lead
                    </span>
                );
            default:
                return (
                    <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        <Snowflake size={13} className="text-slate-500" /> Cold Lead
                    </span>
                );
        }
    };

    return (
        <AdminLayout>
            <Head title="AI Chatbot Leads" />

            <div className="space-y-5 max-w-[1600px] mx-auto pb-12">
                {/* HEADER BANNER */}
                <div className="bg-white rounded-md p-5 border border-slate-200/80 shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div className="flex items-center gap-3.5">
                        <div className="w-11 h-11 rounded-md bg-[#AC6CFF]/15 text-[#AC6CFF] flex items-center justify-center border border-[#AC6CFF]/30 shrink-0">
                            <Bot size={24} />
                        </div>
                        <div>
                            <h1 className="text-xl font-bold text-slate-900 leading-tight">
                                AI Chatbot Leads & Qualifications
                            </h1>
                            <p className="text-sm text-slate-600 mt-0.5">
                                Automated lead capture, AI qualification scoring, and full conversation transcripts.
                            </p>
                        </div>
                    </div>

                    {/* KPI SUMMARY BADGES */}
                    <div className="flex items-center gap-2 flex-wrap">
                        <button
                            onClick={() => handleQualificationChange("all")}
                            className={`px-3 py-1.5 rounded-md text-xs font-bold border transition-all ${
                                qualificationFilter === "all"
                                    ? "bg-slate-900 text-white border-slate-900 shadow-2xs"
                                    : "bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100"
                            }`}
                        >
                            All ({stats.total || 0})
                        </button>
                        <button
                            onClick={() => handleQualificationChange("Hot")}
                            className={`px-3 py-1.5 rounded-md text-xs font-bold border transition-all flex items-center gap-1 ${
                                qualificationFilter === "Hot"
                                    ? "bg-rose-600 text-white border-rose-600 shadow-2xs"
                                    : "bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100"
                            }`}
                        >
                            <Flame size={13} /> Hot ({stats.hot || 0})
                        </button>
                        <button
                            onClick={() => handleQualificationChange("Warm")}
                            className={`px-3 py-1.5 rounded-md text-xs font-bold border transition-all flex items-center gap-1 ${
                                qualificationFilter === "Warm"
                                    ? "bg-amber-600 text-white border-amber-600 shadow-2xs"
                                    : "bg-amber-50 text-amber-800 border-amber-200 hover:bg-amber-100"
                            }`}
                        >
                            <Zap size={13} /> Warm ({stats.warm || 0})
                        </button>
                        <button
                            onClick={() => handleQualificationChange("Cold")}
                            className={`px-3 py-1.5 rounded-md text-xs font-bold border transition-all flex items-center gap-1 ${
                                qualificationFilter === "Cold"
                                    ? "bg-slate-700 text-white border-slate-700 shadow-2xs"
                                    : "bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200"
                            }`}
                        >
                            <Snowflake size={13} /> Cold ({stats.cold || 0})
                        </button>
                    </div>
                </div>

                {/* MAIN CONTENT TABLE */}
                <div className="bg-white rounded-md border border-slate-200/80 shadow-2xs overflow-hidden">
                    {/* SEARCH BAR */}
                    <div className="p-4 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <form onSubmit={handleSearch} className="relative w-full sm:w-[380px]">
                            <Search size={17} className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
                            <input
                                type="text"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Search lead by name, email, or service..."
                                className="w-full h-10 pl-10 pr-4 bg-white border border-slate-200 rounded-md text-sm font-medium focus:outline-none focus:border-[#AC6CFF] focus:ring-1 focus:ring-[#AC6CFF] text-slate-800"
                            />
                        </form>

                        <div className="text-xs font-semibold text-slate-600">
                            Showing {leads.from || 0} - {leads.to || 0} of {leads.total || 0} AI leads
                        </div>
                    </div>

                    {/* TABLE */}
                    <div className="w-full overflow-x-auto overflow-y-hidden touch-pan-x">
                        <table className="w-full min-w-[800px] text-left border-collapse">
                            <thead>
                                <tr className="border-b border-slate-200 bg-slate-50 text-xs font-bold text-slate-700">
                                    <th className="px-4 py-3 whitespace-nowrap">Lead Info</th>
                                    <th className="px-4 py-3 whitespace-nowrap">Interest & Budget</th>
                                    <th className="px-4 py-3 whitespace-nowrap">AI Qualification & Summary</th>
                                    <th className="px-4 py-3 whitespace-nowrap">Status</th>
                                    <th className="px-4 py-3 text-right whitespace-nowrap">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 text-sm">
                                {leads.data && leads.data.length > 0 ? (
                                    leads.data.map((lead) => (
                                        <tr key={lead.id} className="hover:bg-slate-50/80 transition-colors">
                                            {/* 1. Lead Info (Name, Email, Phone) */}
                                            <td className="px-4 py-3.5 whitespace-nowrap">
                                                <div className="flex items-center gap-2.5">
                                                    <div className="w-9 h-9 rounded-full bg-[#AC6CFF]/10 text-[#AC6CFF] flex items-center justify-center border border-[#AC6CFF]/20 font-bold shrink-0 text-xs shadow-2xs">
                                                        {lead.name ? lead.name.charAt(0).toUpperCase() : "G"}
                                                    </div>
                                                    <div className="space-y-0.5">
                                                        <p className="font-bold text-slate-900 text-sm leading-tight">
                                                            {lead.name || "Anonymous Lead"}
                                                        </p>
                                                        {lead.email && (
                                                            <div className="flex items-center gap-1 text-xs text-slate-500 font-medium">
                                                                <Mail size={12} className="text-slate-400 shrink-0" />
                                                                <a href={`mailto:${lead.email}`} className="hover:text-[#AC6CFF] hover:underline truncate max-w-[160px]">
                                                                    {lead.email}
                                                                </a>
                                                            </div>
                                                        )}
                                                        {lead.phone && (
                                                            <div className="flex items-center gap-1 text-xs text-slate-500 font-medium">
                                                                <Phone size={12} className="text-slate-400 shrink-0" />
                                                                <a href={`tel:${lead.phone}`} className="hover:text-[#AC6CFF] hover:underline">
                                                                    {lead.phone}
                                                                </a>
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>
                                            </td>

                                            {/* 2. Interest & Budget */}
                                            <td className="px-4 py-3.5 whitespace-nowrap">
                                                <div className="space-y-1.5">
                                                    {lead.service_interest ? (
                                                        <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                                                            <CheckCircle2 size={11} className="text-emerald-600 shrink-0" />
                                                            {lead.service_interest}
                                                        </span>
                                                    ) : (
                                                        <span className="text-xs text-slate-400 italic">No service selected</span>
                                                    )}
                                                    {lead.budget && (
                                                        <div>
                                                            <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                                <DollarSign size={11} className="text-indigo-600 shrink-0" />
                                                                {lead.budget}
                                                            </span>
                                                        </div>
                                                    )}
                                                </div>
                                            </td>

                                            {/* 3. AI Qualification & Summary */}
                                            <td className="px-4 py-3.5">
                                                <div className="space-y-1 max-w-[280px]">
                                                    <div className="flex items-center gap-2">
                                                        {getQualificationBadge(lead.qualification_status)}
                                                        {(() => {
                                                            const match = (lead.qualification_summary || "").match(/(https?:\/\/[^\s\)]+)/i);
                                                            if (match) {
                                                                return (
                                                                    <a
                                                                        href={match[0]}
                                                                        target="_blank"
                                                                        rel="noopener noreferrer"
                                                                        className="inline-flex items-center gap-1 text-[11px] font-bold text-[#AC6CFF] hover:underline"
                                                                    >
                                                                        <ExternalLink size={11} /> Target Link
                                                                    </a>
                                                                );
                                                            }
                                                            return null;
                                                        })()}
                                                    </div>
                                                    {lead.qualification_summary && (
                                                        <p className="text-xs text-slate-600 leading-relaxed truncate">
                                                            {lead.qualification_summary.replace(/https?:\/\/[^\s\)]+/gi, '').trim()}
                                                        </p>
                                                    )}
                                                </div>
                                            </td>

                                            {/* 4. Status */}
                                            <td className="px-4 py-3.5 whitespace-nowrap">
                                                <div className="relative inline-block min-w-[125px]">
                                                    <select
                                                        value={lead.status || "new"}
                                                        onChange={(e) => handleStatusChange(lead.id, e.target.value)}
                                                        className={`w-full h-8 pl-3 pr-8 rounded-lg text-xs font-bold border transition-all appearance-none cursor-pointer focus:outline-none focus:ring-2 shadow-2xs ${getStatusSelectStyle(lead.status)}`}
                                                    >
                                                        <option value="new" className="bg-white text-slate-800 font-semibold py-1">New</option>
                                                        <option value="contacted" className="bg-white text-slate-800 font-semibold py-1">Contacted</option>
                                                        <option value="converted" className="bg-white text-slate-800 font-semibold py-1">Converted</option>
                                                        <option value="closed" className="bg-white text-slate-800 font-semibold py-1">Closed</option>
                                                    </select>
                                                    <ChevronDown size={13} className="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none opacity-60" />
                                                </div>
                                            </td>


                                            {/* 5. Actions */}
                                            <td className="px-4 py-3.5 text-right whitespace-nowrap">
                                                <div className="flex items-center justify-end gap-2">
                                                    <Link
                                                        href={lead.user_id ? `/admin/messages?user_id=${lead.user_id}` : '/admin/messages'}
                                                        className="px-2.5 py-1.5 rounded-md text-xs font-bold bg-[#0084ff]/10 text-[#0084ff] hover:bg-[#0084ff] hover:text-white border border-[#0084ff]/30 transition-all flex items-center gap-1 shadow-2xs cursor-pointer"
                                                        title="Open Messenger Chat with Lead"
                                                    >
                                                        <MessageSquare size={13} /> Live Chat
                                                    </Link>
                                                    <button
                                                        onClick={() => setSelectedChatLead(lead)}
                                                        className="px-2 py-1.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200 transition-all flex items-center gap-1 cursor-pointer"
                                                        title="View AI Chatbot Transcript"
                                                    >
                                                        Transcript
                                                    </button>
                                                    <button
                                                        onClick={() => handleDelete(lead.id)}
                                                        className="w-7 h-7 flex items-center justify-center rounded-md text-rose-600 hover:bg-rose-50 border border-slate-200 transition-all cursor-pointer"
                                                        title="Delete Lead"
                                                    >
                                                        <Trash2 size={14} />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="5" className="px-5 py-12 text-center text-slate-400 text-sm">
                                            No AI Chatbot leads captured yet.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {/* CHAT LOG MODAL */}
            {selectedChatLead && (
                <div className="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
                    <div className="bg-white rounded-xl shadow-2xl border border-slate-200 w-full max-w-2xl overflow-hidden flex flex-col max-h-[85vh]">
                        {/* Modal Header */}
                        <div className="p-4 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div className="flex items-center gap-3">
                                <div className="w-10 h-10 rounded-full bg-[#AC6CFF]/10 text-[#AC6CFF] flex items-center justify-center border border-[#AC6CFF]/30">
                                    <Bot size={20} />
                                </div>
                                <div>
                                    <h3 className="font-bold text-slate-900 text-sm">
                                        Chat Log: {selectedChatLead.name || "Anonymous Lead"}
                                    </h3>
                                    <p className="text-xs text-slate-500">
                                        {selectedChatLead.email || "No email provided"} • {selectedChatLead.qualification_status} Lead
                                    </p>
                                </div>
                            </div>
                            <button
                                onClick={() => setSelectedChatLead(null)}
                                className="text-slate-400 hover:text-slate-700 p-1.5 rounded-md hover:bg-slate-100 transition-colors"
                            >
                                <X size={20} />
                            </button>
                        </div>

                        {/* Modal Body - Conversation Log */}
                        <div className="p-4 flex-1 overflow-y-auto space-y-3 bg-slate-50/40">
                            {selectedChatLead.chat_history && selectedChatLead.chat_history.length > 0 ? (
                                selectedChatLead.chat_history.map((msg, index) => (
                                    <div
                                        key={index}
                                        className={`flex flex-col ${msg.sender === "user" ? "items-end" : "items-start"}`}
                                    >
                                        <div
                                            className={`p-3 rounded-xl max-w-[85%] text-xs leading-relaxed ${
                                                msg.sender === "user"
                                                    ? "bg-slate-900 text-white rounded-br-none"
                                                    : "bg-white text-slate-800 border border-slate-200 shadow-2xs rounded-bl-none"
                                            }`}
                                        >
                                            <p className="font-semibold text-[10px] opacity-75 mb-0.5">
                                                {msg.sender === "user" ? (selectedChatLead.name || "Lead") : "AI Assistant"}
                                            </p>
                                            <p className="whitespace-pre-wrap">{msg.message}</p>
                                        </div>
                                        <span className="text-[10px] text-slate-400 mt-1 px-1">
                                            {msg.timestamp || ""}
                                        </span>
                                    </div>
                                ))
                            ) : (
                                <p className="text-center text-slate-400 text-xs py-8">
                                    No transcript history recorded for this lead.
                                </p>
                            )}
                        </div>

                        {/* Modal Footer */}
                        <div className="p-3.5 border-t border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <Link
                                href={selectedChatLead.user_id ? `/admin/messages?user_id=${selectedChatLead.user_id}` : '/admin/messages'}
                                className="px-4 py-2 bg-gradient-to-r from-[#0084ff] to-[#00c6ff] text-white rounded-lg text-xs font-extrabold hover:opacity-90 transition-all inline-flex items-center gap-1.5 shadow-2xs"
                            >
                                <MessageSquare size={14} /> Open Live Messenger Chat ↗
                            </Link>

                            <button
                                onClick={() => setSelectedChatLead(null)}
                                className="px-4 py-2 bg-white text-slate-700 border border-slate-200 rounded-lg text-xs font-bold hover:bg-slate-100 transition-colors"
                            >
                                Close Transcript
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </AdminLayout>
    );
}