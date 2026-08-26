import React, { useState } from "react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Head, useForm } from "@inertiajs/react";
import {
    Mail,
    Key,
    Loader2,
    Cpu,
    CheckCircle2,
    Eye,
    EyeOff,
    Copy,
    Check,
    Cog,
    ChevronDown,
} from "lucide-react";

export default function SystemSettings({ settings }) {
    const [activeTab, setActiveTab] = useState("email");
    const [showKey, setShowKey] = useState(false);
    const [copied, setCopied] = useState(false);

    const handleCopy = () => {
        if (data.openai_api_key) {
            navigator.clipboard.writeText(data.openai_api_key);
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        }
    };

    const { data, setData, post, processing, errors } = useForm({
        mail_driver: settings.mail_driver || "smtp",
        mail_host: settings.mail_host || "",
        mail_port: settings.mail_port || "587",
        mail_username: settings.mail_username || "",
        mail_password: settings.mail_password || "",
        mail_encryption: settings.mail_encryption || "tls",
        mail_from_address: settings.mail_from_address || "",
        mail_from_name: settings.mail_from_name || "",
        openai_api_key: settings.openai_api_key || "",
        email_footer: settings.email_footer || "",
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route("admin.settings.system.update"));
    };

    const tabs = [
        { id: "email", label: "Email Server", icon: Mail },
        { id: "api", label: "AI & API Tools", icon: Cpu },
    ];

    return (
        <AdminLayout>
            <Head title="System Settings" />

            <div className="space-y-4 max-w-[1200px] mx-auto pb-12">
                {/* COMPACT TOP HEADER */}
                <div className="bg-white rounded-md p-4 border border-slate-200/80 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div className="flex items-center gap-2">
                        <Cog size={20} className="text-[#0a66c2]" />
                        <div>
                            <h1 className="text-lg font-bold text-slate-800 leading-tight">
                                System Configuration
                            </h1>
                            <p className="text-xs text-slate-500 mt-0.5">
                                Configure mail SMTP servers, AI integrations, and system API credentials.
                            </p>
                        </div>
                    </div>

                    {/* COMPACT TABS */}
                    <div className="flex items-center gap-1 bg-slate-100 p-1 rounded-md border border-slate-200 self-start sm:self-auto">
                        {tabs.map((tab) => (
                            <button
                                key={tab.id}
                                type="button"
                                onClick={() => setActiveTab(tab.id)}
                                className={`flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-semibold transition-all ${
                                    activeTab === tab.id
                                        ? "bg-white text-[#0a66c2] shadow-2xs border border-slate-200/80"
                                        : "text-slate-600 hover:text-slate-900"
                                }`}
                            >
                                <tab.icon size={14} />
                                {tab.label}
                            </button>
                        ))}
                    </div>
                </div>

                <form onSubmit={handleSubmit} className="space-y-4">
                    {/* EMAIL SERVER TAB */}
                    {activeTab === "email" && (
                        <div className="bg-white rounded-md border border-slate-200/80 shadow-2xs p-5 space-y-5">
                            {/* SMTP Server Details */}
                            <div className="space-y-3">
                                <h3 className="text-xs font-bold text-slate-800 border-b border-slate-100 pb-2">
                                    SMTP Server Details
                                </h3>
                                <div className="grid grid-cols-1 md:grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div className="space-y-1">
                                        <label className="block text-xs font-bold text-slate-700">
                                            Mail Driver
                                        </label>
                                        <input
                                            type="text"
                                            value={data.mail_driver}
                                            onChange={(e) => setData("mail_driver", e.target.value)}
                                            className="w-full h-9 px-3 bg-white border border-slate-200 rounded-md text-xs font-medium focus:border-[#0a66c2] focus:ring-1 focus:ring-[#0a66c2] outline-none"
                                        />
                                    </div>
                                    <div className="space-y-1">
                                        <label className="block text-xs font-bold text-slate-700">
                                            SMTP Host
                                        </label>
                                        <input
                                            type="text"
                                            value={data.mail_host}
                                            onChange={(e) => setData("mail_host", e.target.value)}
                                            placeholder="smtp.mailtrap.io"
                                            className="w-full h-9 px-3 bg-white border border-slate-200 rounded-md text-xs font-medium focus:border-[#0a66c2] focus:ring-1 focus:ring-[#0a66c2] outline-none"
                                        />
                                    </div>
                                    <div className="space-y-1">
                                        <label className="block text-xs font-bold text-slate-700">
                                            Port
                                        </label>
                                        <input
                                            type="text"
                                            value={data.mail_port}
                                            onChange={(e) => setData("mail_port", e.target.value)}
                                            placeholder="587"
                                            className="w-full h-9 px-3 bg-white border border-slate-200 rounded-md text-xs font-medium focus:border-[#0a66c2] focus:ring-1 focus:ring-[#0a66c2] outline-none"
                                        />
                                    </div>
                                    <div className="space-y-1">
                                        <label className="block text-xs font-bold text-slate-700">
                                            Encryption
                                        </label>
                                        <div className="relative">
                                            <select
                                                value={data.mail_encryption}
                                                onChange={(e) => setData("mail_encryption", e.target.value)}
                                                className="w-full h-9 pl-3 pr-8 bg-white border border-slate-200 rounded-md text-xs font-semibold text-slate-800 appearance-none cursor-pointer focus:border-[#0a66c2] outline-none"
                                            >
                                                <option value="tls">TLS</option>
                                                <option value="ssl">SSL</option>
                                                <option value="none">None</option>
                                            </select>
                                            <div className="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                                <ChevronDown size={14} />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Authentication & Identity */}
                            <div className="space-y-3 pt-3 border-t border-slate-100">
                                <h3 className="text-xs font-bold text-slate-800 border-b border-slate-100 pb-2">
                                    Authentication & Sender Identity
                                </h3>
                                <div className="grid grid-cols-1 md:grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div className="space-y-1">
                                        <label className="block text-xs font-bold text-slate-700">
                                            Username
                                        </label>
                                        <input
                                            type="text"
                                            value={data.mail_username}
                                            onChange={(e) => setData("mail_username", e.target.value)}
                                            className="w-full h-9 px-3 bg-white border border-slate-200 rounded-md text-xs font-medium focus:border-[#0a66c2] focus:ring-1 focus:ring-[#0a66c2] outline-none"
                                        />
                                    </div>
                                    <div className="space-y-1">
                                        <label className="block text-xs font-bold text-slate-700">
                                            Password
                                        </label>
                                        <input
                                            type="password"
                                            value={data.mail_password}
                                            onChange={(e) => setData("mail_password", e.target.value)}
                                            className="w-full h-9 px-3 bg-white border border-slate-200 rounded-md text-xs font-medium focus:border-[#0a66c2] focus:ring-1 focus:ring-[#0a66c2] outline-none"
                                        />
                                    </div>
                                    <div className="space-y-1">
                                        <label className="block text-xs font-bold text-slate-700">
                                            From Email Address
                                        </label>
                                        <input
                                            type="email"
                                            value={data.mail_from_address}
                                            onChange={(e) => setData("mail_from_address", e.target.value)}
                                            placeholder="noreply@example.com"
                                            className="w-full h-9 px-3 bg-white border border-slate-200 rounded-md text-xs font-medium focus:border-[#0a66c2] focus:ring-1 focus:ring-[#0a66c2] outline-none"
                                        />
                                    </div>
                                    <div className="space-y-1">
                                        <label className="block text-xs font-bold text-slate-700">
                                            From Name
                                        </label>
                                        <input
                                            type="text"
                                            value={data.mail_from_name}
                                            onChange={(e) => setData("mail_from_name", e.target.value)}
                                            placeholder="Company Name"
                                            className="w-full h-9 px-3 bg-white border border-slate-200 rounded-md text-xs font-medium focus:border-[#0a66c2] focus:ring-1 focus:ring-[#0a66c2] outline-none"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* AI & API TOOLS TAB */}
                    {activeTab === "api" && (
                        <div className="bg-white rounded-md border border-slate-200/80 shadow-2xs p-5 space-y-3">
                            <h3 className="text-xs font-bold text-slate-800 border-b border-slate-100 pb-2">
                                External AI & API Integrations
                            </h3>
                            <div className="space-y-1 max-w-[600px]">
                                <label className="block text-xs font-bold text-slate-700">
                                    OpenAI API Key
                                </label>
                                <div className="relative">
                                    <div className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                        <Key size={15} />
                                    </div>
                                    <input
                                        type={showKey ? "text" : "password"}
                                        value={data.openai_api_key}
                                        onChange={(e) => setData("openai_api_key", e.target.value)}
                                        className="w-full h-9 pl-9 pr-20 bg-white border border-slate-200 rounded-md text-xs font-medium focus:border-[#0a66c2] focus:ring-1 focus:ring-[#0a66c2] outline-none"
                                        placeholder="sk-..."
                                    />
                                    <div className="absolute right-1.5 top-1/2 -translate-y-1/2 flex items-center gap-1">
                                        <button
                                            type="button"
                                            onClick={() => setShowKey(!showKey)}
                                            className="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-md transition-all"
                                            title={showKey ? "Hide API Key" : "Show API Key"}
                                        >
                                            {showKey ? <EyeOff size={14} /> : <Eye size={14} />}
                                        </button>
                                        <button
                                            type="button"
                                            onClick={handleCopy}
                                            className="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-md transition-all"
                                            title="Copy API Key"
                                        >
                                            {copied ? <Check size={14} className="text-emerald-600" /> : <Copy size={14} />}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* ACTIONS */}
                    <div className="flex items-center justify-end gap-2 pt-2">
                        <button
                            type="button"
                            className="h-9 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-md text-xs font-semibold transition-all"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            disabled={processing}
                            className="h-9 px-4 bg-[#0a66c2] hover:bg-[#084e96] text-white rounded-md text-xs font-semibold transition-all flex items-center gap-1.5 disabled:opacity-50 shadow-2xs"
                        >
                            {processing ? (
                                <Loader2 className="animate-spin" size={14} />
                            ) : (
                                <CheckCircle2 size={14} />
                            )}
                            {processing ? "Saving..." : "Save Changes"}
                        </button>
                    </div>
                </form>
            </div>
        </AdminLayout>
    );
}