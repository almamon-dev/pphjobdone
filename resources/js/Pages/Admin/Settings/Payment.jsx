import React, { useState } from "react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Head, useForm, Link } from "@inertiajs/react";
import {
    CreditCard,
    Key,
    Shield,
    Loader2,
    ChevronRight,
    Home,
    CheckCircle2,
    Eye,
    EyeOff,
    Copy,
    Check,
    AlertCircle
} from "lucide-react";

export default function PaymentSettings({ settings }) {
    const [showKey, setShowKey] = useState(false);
    const [showSecret, setShowSecret] = useState(false);
    const [showWebhookSecret, setShowWebhookSecret] = useState(false);
    const [copiedKey, setCopiedKey] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        stripe_key: settings.stripe_key || "",
        stripe_secret: settings.stripe_secret || "",
        stripe_webhook_secret: settings.stripe_webhook_secret || "",
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route("admin.settings.payment.update"));
    };

    const handleCopy = (value, setCopied) => {
        if (value) {
            navigator.clipboard.writeText(value);
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        }
    };

    return (
        <AdminLayout>
            <Head title="Payment Settings" />

            <div className="max-w-9xl mx-auto pb-20 pt-4 px-4 sm:px-0 space-y-6">
                {/* Minimal Breadcrumbs */}
                <nav className="flex items-center gap-2 text-[12px] font-medium text-slate-400/80">
                    <Link
                        href="/dashboard"
                        className="hover:text-[#673ab7] transition-colors flex items-center gap-1.5"
                    >
                        <Home size={13} />
                        Dashboard
                    </Link>
                    <ChevronRight size={12} className="opacity-50" />
                    <span>Settings</span>
                    <ChevronRight size={12} className="opacity-50" />
                    <span className="text-slate-600">Payment</span>
                </nav>

                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-6">
                    <div>
                        <h1 className="text-[24px] font-bold text-[#2f3344] tracking-tight">
                            Payment Configuration
                        </h1>
                        <p className="text-[13px] text-slate-400 mt-0.5">
                            Manage Stripe API keys and Webhook settings
                        </p>
                    </div>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="bg-white rounded-xl border border-slate-100 shadow-sm p-6 sm:p-8 space-y-8 animate-in fade-in duration-300">
                        <div className="space-y-6">
                            <h3 className="text-[14px] font-bold text-[#2f3344] flex items-center gap-2">
                                <div className="w-1.5 h-1.5 rounded-full bg-[#673ab7]"></div>
                                Stripe API Credentials
                            </h3>
                            
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div className="space-y-1.5">
                                    <label className="text-[13px] font-semibold text-slate-600">
                                        Stripe Publishable Key
                                    </label>
                                    <div className="relative">
                                        <div className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300">
                                            <CreditCard size={16} />
                                        </div>
                                        <input
                                            type={showKey ? "text" : "password"}
                                            value={data.stripe_key}
                                            onChange={(e) => setData("stripe_key", e.target.value)}
                                            className="w-full bg-slate-50/50 border-slate-200 rounded-lg pl-10 pr-20 py-2.5 text-[14px] focus:bg-white focus:ring-2 focus:ring-[#673ab7]/10 focus:border-[#673ab7] transition-all"
                                            placeholder="pk_test_..."
                                        />
                                        <div className="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-1">
                                            <button
                                                type="button"
                                                onClick={() => setShowKey(!showKey)}
                                                className="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-md transition-all"
                                                title={showKey ? "Hide Key" : "Show Key"}
                                            >
                                                {showKey ? <EyeOff size={14} /> : <Eye size={14} />}
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div className="space-y-1.5">
                                    <label className="text-[13px] font-semibold text-slate-600">
                                        Stripe Secret Key
                                    </label>
                                    <div className="relative">
                                        <div className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300">
                                            <Key size={16} />
                                        </div>
                                        <input
                                            type={showSecret ? "text" : "password"}
                                            value={data.stripe_secret}
                                            onChange={(e) => setData("stripe_secret", e.target.value)}
                                            className="w-full bg-slate-50/50 border-slate-200 rounded-lg pl-10 pr-20 py-2.5 text-[14px] focus:bg-white focus:ring-2 focus:ring-[#673ab7]/10 focus:border-[#673ab7] transition-all"
                                            placeholder="sk_test_..."
                                        />
                                        <div className="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-1">
                                            <button
                                                type="button"
                                                onClick={() => setShowSecret(!showSecret)}
                                                className="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-md transition-all"
                                                title={showSecret ? "Hide Key" : "Show Key"}
                                            >
                                                {showSecret ? <EyeOff size={14} /> : <Eye size={14} />}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="space-y-6 pt-6 border-t border-slate-50">
                            <h3 className="text-[14px] font-bold text-[#2f3344] flex items-center gap-2">
                                <div className="w-1.5 h-1.5 rounded-full bg-[#00b090]"></div>
                                Webhook Configuration
                            </h3>
                            
                            <div className="space-y-1.5 max-w-2xl">
                                <label className="text-[13px] font-semibold text-slate-600">
                                    Stripe Webhook Secret
                                </label>
                                <div className="relative">
                                    <div className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300">
                                        <Shield size={16} />
                                    </div>
                                    <input
                                        type={showWebhookSecret ? "text" : "password"}
                                        value={data.stripe_webhook_secret}
                                        onChange={(e) => setData("stripe_webhook_secret", e.target.value)}
                                        className="w-full bg-slate-50/50 border-slate-200 rounded-lg pl-10 pr-20 py-2.5 text-[14px] focus:bg-white focus:ring-2 focus:ring-[#673ab7]/10 focus:border-[#673ab7] transition-all"
                                        placeholder="whsec_..."
                                    />
                                    <div className="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-1">
                                        <button
                                            type="button"
                                            onClick={() => setShowWebhookSecret(!showWebhookSecret)}
                                            className="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-md transition-all"
                                            title={showWebhookSecret ? "Hide Secret" : "Show Secret"}
                                        >
                                            {showWebhookSecret ? <EyeOff size={14} /> : <Eye size={14} />}
                                        </button>
                                    </div>
                                </div>
                                <p className="text-[12px] text-slate-500 mt-2">
                                    This is used to verify that incoming webhooks are actually from Stripe.
                                </p>
                            </div>

                            {/* Webhook Quick Setup Guide */}
                            <div className="mt-6 bg-[#0a66c2]/5 border border-[#0a66c2]/10 rounded-xl p-5">
                                <div className="flex items-start gap-3">
                                    <AlertCircle className="text-[#0a66c2] mt-0.5" size={18} />
                                    <div>
                                        <h4 className="text-[13px] font-bold text-[#0a66c2] mb-1">
                                            Webhook Quick Setup
                                        </h4>
                                        <p className="text-[13px] text-slate-600 mb-3">
                                            When setting up your webhook in the Stripe Dashboard, point it to <strong>{window.location.origin}/webhook</strong> and listen to the following events:
                                        </p>
                                        <ul className="list-disc pl-5 space-y-1.5 text-[13px] text-slate-700 font-medium">
                                            <li><code>checkout.session.completed</code></li>
                                            <li><code>payment_intent.succeeded</code></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Actions */}
                    <div className="flex items-center justify-end gap-3 pt-6">
                        <button
                            type="button"
                            className="bg-slate-100 text-slate-600 px-6 py-2 rounded-lg text-[13px] font-bold hover:bg-slate-200 transition-all"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            disabled={processing}
                            className="bg-[#673ab7] text-white px-6 py-2 rounded-lg text-[13px] font-bold hover:bg-[#5e35b1] transition-all flex items-center gap-2 disabled:opacity-50 shadow-lg shadow-[#673ab7]/10"
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
