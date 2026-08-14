import React, { useState } from "react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Head, useForm } from "@inertiajs/react";
import {
    CreditCard,
    Key,
    Shield,
    Loader2,
    CheckCircle2,
    Eye,
    EyeOff,
    AlertCircle,
} from "lucide-react";

export default function PaymentSettings({ settings }) {
    const [showKey, setShowKey] = useState(false);
    const [showSecret, setShowSecret] = useState(false);
    const [showWebhookSecret, setShowWebhookSecret] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        stripe_key: settings.stripe_key || "",
        stripe_secret: settings.stripe_secret || "",
        stripe_webhook_secret: settings.stripe_webhook_secret || "",
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route("admin.settings.payment.update"));
    };

    return (
        <AdminLayout>
            <Head title="Payment Settings" />

            <div className="space-y-4 max-w-[1200px] mx-auto pb-12">
                {/* COMPACT TOP HEADER */}
                <div className="bg-white rounded-md p-4 border border-slate-200/80 shadow-2xs flex items-center justify-between">
                    <div className="flex items-center gap-2">
                        <CreditCard size={20} className="text-[#0a66c2]" />
                        <div>
                            <h1 className="text-lg font-bold text-slate-800 leading-tight">
                                Payment Configuration
                            </h1>
                            <p className="text-xs text-slate-500 mt-0.5">
                                Manage Stripe payment gateway API keys and webhook security settings.
                            </p>
                        </div>
                    </div>
                </div>

                <form onSubmit={handleSubmit} className="space-y-4">
                    <div className="bg-white rounded-md border border-slate-200/80 shadow-2xs p-5 space-y-5">
                        {/* API Credentials */}
                        <div className="space-y-3">
                            <h3 className="text-xs font-bold text-slate-800 border-b border-slate-100 pb-2">
                                Stripe API Credentials
                            </h3>
                            
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                                {/* Publishable Key */}
                                <div className="space-y-1">
                                    <label className="block text-xs font-bold text-slate-700">
                                        Stripe Publishable Key
                                    </label>
                                    <div className="relative">
                                        <div className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                            <CreditCard size={15} />
                                        </div>
                                        <input
                                            type={showKey ? "text" : "password"}
                                            value={data.stripe_key}
                                            onChange={(e) => setData("stripe_key", e.target.value)}
                                            className="w-full h-9 pl-9 pr-10 bg-white border border-slate-200 rounded-md text-xs font-medium focus:border-[#0a66c2] focus:ring-1 focus:ring-[#0a66c2] outline-none"
                                            placeholder="pk_test_..."
                                        />
                                        <button
                                            type="button"
                                            onClick={() => setShowKey(!showKey)}
                                            className="absolute right-1.5 top-1/2 -translate-y-1/2 w-7 h-7 flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-md transition-all"
                                            title={showKey ? "Hide Key" : "Show Key"}
                                        >
                                            {showKey ? <EyeOff size={14} /> : <Eye size={14} />}
                                        </button>
                                    </div>
                                </div>

                                {/* Secret Key */}
                                <div className="space-y-1">
                                    <label className="block text-xs font-bold text-slate-700">
                                        Stripe Secret Key
                                    </label>
                                    <div className="relative">
                                        <div className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                            <Key size={15} />
                                        </div>
                                        <input
                                            type={showSecret ? "text" : "password"}
                                            value={data.stripe_secret}
                                            onChange={(e) => setData("stripe_secret", e.target.value)}
                                            className="w-full h-9 pl-9 pr-10 bg-white border border-slate-200 rounded-md text-xs font-medium focus:border-[#0a66c2] focus:ring-1 focus:ring-[#0a66c2] outline-none"
                                            placeholder="sk_test_..."
                                        />
                                        <button
                                            type="button"
                                            onClick={() => setShowSecret(!showSecret)}
                                            className="absolute right-1.5 top-1/2 -translate-y-1/2 w-7 h-7 flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-md transition-all"
                                            title={showSecret ? "Hide Key" : "Show Key"}
                                        >
                                            {showSecret ? <EyeOff size={14} /> : <Eye size={14} />}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Webhook Configuration */}
                        <div className="space-y-3 pt-3 border-t border-slate-100">
                            <h3 className="text-xs font-bold text-slate-800 border-b border-slate-100 pb-2">
                                Webhook Security & Setup
                            </h3>
                            
                            <div className="space-y-1 max-w-[600px]">
                                <label className="block text-xs font-bold text-slate-700">
                                    Stripe Webhook Secret
                                </label>
                                <div className="relative">
                                    <div className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                        <Shield size={15} />
                                    </div>
                                    <input
                                        type={showWebhookSecret ? "text" : "password"}
                                        value={data.stripe_webhook_secret}
                                        onChange={(e) => setData("stripe_webhook_secret", e.target.value)}
                                        className="w-full h-9 pl-9 pr-10 bg-white border border-slate-200 rounded-md text-xs font-medium focus:border-[#0a66c2] focus:ring-1 focus:ring-[#0a66c2] outline-none"
                                        placeholder="whsec_..."
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowWebhookSecret(!showWebhookSecret)}
                                        className="absolute right-1.5 top-1/2 -translate-y-1/2 w-7 h-7 flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-md transition-all"
                                        title={showWebhookSecret ? "Hide Secret" : "Show Secret"}
                                    >
                                        {showWebhookSecret ? <EyeOff size={14} /> : <Eye size={14} />}
                                    </button>
                                </div>
                            </div>

                            {/* Webhook Quick Setup Guide */}
                            <div className="mt-3 bg-[#0a66c2]/5 border border-[#0a66c2]/20 rounded-md p-3.5">
                                <div className="flex items-start gap-2.5">
                                    <AlertCircle className="text-[#0a66c2] shrink-0 mt-0.5" size={16} />
                                    <div>
                                        <h4 className="text-xs font-bold text-[#0a66c2] mb-1">
                                            Webhook Endpoint & Events
                                        </h4>
                                        <p className="text-xs text-slate-700 mb-2">
                                            Configure your Stripe Dashboard Webhook to point to: <strong className="font-mono text-slate-900 bg-white px-1.5 py-0.5 rounded border border-slate-200">{typeof window !== 'undefined' ? window.location.origin : ''}/webhook</strong>
                                        </p>
                                        <ul className="list-disc pl-4 space-y-0.5 text-xs text-slate-700 font-medium">
                                            <li><code className="font-mono text-slate-900">checkout.session.completed</code></li>
                                            <li><code className="font-mono text-slate-900">payment_intent.succeeded</code></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Actions */}
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
