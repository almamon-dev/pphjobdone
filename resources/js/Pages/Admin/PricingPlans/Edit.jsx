import React from "react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Head, Link, useForm } from "@inertiajs/react";
import {
    DollarSign,
    Plus,
    Trash2,
    CheckCircle2,
    Settings,
    CircleDollarSign,
    ArrowLeft,
} from "lucide-react";

export default function Edit({ pricing_plan, services }) {
    const { data, setData, put, processing, errors } = useForm({
        service_ids: pricing_plan.services.map((s) => s.id) || [],
        name: pricing_plan.name || "",
        price: pricing_plan.price || "",
        subtitle: pricing_plan.subtitle || "",
        is_popular: pricing_plan.is_popular ? true : false,
        features: pricing_plan.features || [""],
        button_text: pricing_plan.button_text || "Get Started",
        status: pricing_plan.status ? true : false,
    });

    const addFeature = () => {
        setData("features", [...data.features, ""]);
    };

    const removeFeature = (index) => {
        const newFeatures = data.features.filter((_, i) => i !== index);
        setData("features", newFeatures);
    };

    const updateFeature = (index, value) => {
        const newFeatures = [...data.features];
        newFeatures[index] = value;
        setData("features", newFeatures);
    };

    const handleServiceToggle = (id) => {
        const currentIds = [...data.service_ids];
        if (currentIds.includes(id)) {
            setData(
                "service_ids",
                currentIds.filter((i) => i !== id)
            );
        } else {
            setData("service_ids", [...currentIds, id]);
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route("admin.pricing-plans.update", pricing_plan.id));
    };

    return (
        <AdminLayout>
            <Head title={`Edit Plan: ${pricing_plan.name}`} />

            <div className="space-y-4 max-w-[1200px] mx-auto pb-12">
                {/* COMPACT TOP HEADER */}
                <div className="bg-white rounded-md p-4 border border-slate-200/80 shadow-2xs flex items-center justify-between">
                    <div className="flex items-center gap-2">
                        <CircleDollarSign size={20} className="text-[#0a66c2]" />
                        <div>
                            <h1 className="text-lg font-bold text-slate-800 leading-tight">
                                Edit Pricing Plan
                            </h1>
                            <p className="text-xs text-slate-500 mt-0.5">
                                Update pricing tier features, assigned services, and billing rate.
                            </p>
                        </div>
                    </div>

                    <Link
                        href={route("admin.pricing-plans.index")}
                        className="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 rounded-md text-xs font-semibold transition-all flex items-center gap-1.5"
                    >
                        <ArrowLeft size={14} /> Back to Plans
                    </Link>
                </div>

                <form onSubmit={handleSubmit} className="grid grid-cols-12 gap-4 items-start">
                    {/* Left Column: Basic Info & Features */}
                    <div className="col-span-12 lg:col-span-8 space-y-4">
                        {/* Plan Details */}
                        <div className="bg-white rounded-md border border-slate-200/80 shadow-2xs p-5">
                            <div className="flex items-center gap-2 mb-4 pb-2 border-b border-slate-100">
                                <DollarSign size={16} className="text-[#0a66c2]" />
                                <h2 className="text-xs font-bold text-slate-800">
                                    Plan Details & Services
                                </h2>
                            </div>

                            <div className="space-y-3.5">
                                {/* Services Selection Checkboxes */}
                                <div className="space-y-1">
                                    <label className="block text-xs font-bold text-slate-700">
                                        Assigned Services <span className="text-rose-500">*</span>
                                    </label>
                                    <div className="flex flex-wrap gap-2 p-3 border border-slate-200 rounded-md bg-slate-50/50">
                                        {services.map((service) => (
                                            <label
                                                key={service.id}
                                                className={`flex items-center gap-2 px-2.5 py-1 rounded-md border text-xs font-medium cursor-pointer transition-all ${
                                                    data.service_ids.includes(service.id)
                                                        ? "bg-[#0a66c2]/10 border-[#0a66c2]/30 text-[#0a66c2]"
                                                        : "bg-white border-slate-200 text-slate-700 hover:bg-slate-100"
                                                }`}
                                            >
                                                <input
                                                    type="checkbox"
                                                    checked={data.service_ids.includes(service.id)}
                                                    onChange={() => handleServiceToggle(service.id)}
                                                    className="w-3.5 h-3.5 rounded border-slate-300 text-[#0a66c2] focus:ring-[#0a66c2]"
                                                />
                                                <span>{service.title}</span>
                                            </label>
                                        ))}
                                    </div>
                                    {errors.service_ids && (
                                        <p className="text-rose-500 text-[11px] mt-0.5">
                                            {errors.service_ids}
                                        </p>
                                    )}
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    {/* Plan Name */}
                                    <div className="space-y-1">
                                        <label className="block text-xs font-bold text-slate-700">
                                            Plan Name <span className="text-rose-500">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            value={data.name}
                                            onChange={(e) => setData("name", e.target.value)}
                                            placeholder="e.g., Enterprise Plan"
                                            className={`w-full h-9 px-3 border ${
                                                errors.name ? "border-rose-500" : "border-slate-200"
                                            } rounded-md text-xs focus:ring-1 focus:ring-[#0a66c2] focus:border-[#0a66c2] outline-none transition-all`}
                                        />
                                        {errors.name && (
                                            <p className="text-rose-500 text-[11px] mt-0.5">
                                                {errors.name}
                                            </p>
                                        )}
                                    </div>

                                    {/* Price */}
                                    <div className="space-y-1">
                                        <label className="block text-xs font-bold text-slate-700">
                                            Price <span className="text-rose-500">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            value={data.price}
                                            onChange={(e) => setData("price", e.target.value)}
                                            placeholder="e.g., 299 or $19.99/mo"
                                            className={`w-full h-9 px-3 border ${
                                                errors.price ? "border-rose-500" : "border-slate-200"
                                            } rounded-md text-xs focus:ring-1 focus:ring-[#0a66c2] focus:border-[#0a66c2] outline-none transition-all`}
                                        />
                                        {errors.price && (
                                            <p className="text-rose-500 text-[11px] mt-0.5">
                                                {errors.price}
                                            </p>
                                        )}
                                    </div>
                                </div>

                                {/* Subtitle */}
                                <div className="space-y-1">
                                    <label className="block text-xs font-bold text-slate-700">
                                        Subtitle
                                    </label>
                                    <input
                                        type="text"
                                        value={data.subtitle}
                                        onChange={(e) => setData("subtitle", e.target.value)}
                                        placeholder="e.g., Best for growing businesses"
                                        className="w-full h-9 px-3 border border-slate-200 rounded-md text-xs focus:ring-1 focus:ring-[#0a66c2] focus:border-[#0a66c2] outline-none"
                                    />
                                    {errors.subtitle && (
                                        <p className="text-rose-500 text-[11px] mt-0.5">
                                            {errors.subtitle}
                                        </p>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Plan Features */}
                        <div className="bg-white rounded-md border border-slate-200/80 shadow-2xs p-5">
                            <div className="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                                <div className="flex items-center gap-2">
                                    <CheckCircle2 size={16} className="text-[#0a66c2]" />
                                    <h2 className="text-xs font-bold text-slate-800">
                                        Included Plan Features
                                    </h2>
                                </div>
                                <button
                                    type="button"
                                    onClick={addFeature}
                                    className="text-[#0a66c2] text-xs font-semibold hover:underline flex items-center gap-1"
                                >
                                    <Plus size={14} /> Add Feature
                                </button>
                            </div>

                            <div className="space-y-2">
                                {data.features.map((feature, index) => (
                                    <div key={index} className="flex gap-2 items-center">
                                        <input
                                            type="text"
                                            value={feature}
                                            onChange={(e) => updateFeature(index, e.target.value)}
                                            placeholder="e.g., 24/7 Priority Support"
                                            className="flex-1 h-8 px-3 border border-slate-200 rounded-md text-xs focus:border-[#0a66c2] outline-none"
                                        />
                                        {data.features.length > 1 && (
                                            <button
                                                type="button"
                                                onClick={() => removeFeature(index)}
                                                className="w-8 h-8 flex items-center justify-center rounded-md text-rose-500 hover:bg-rose-50 border border-slate-200 transition-all shrink-0"
                                                title="Remove feature"
                                            >
                                                <Trash2 size={14} />
                                            </button>
                                        )}
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* Right Column: Settings & Publish */}
                    <div className="col-span-12 lg:col-span-4 space-y-4">
                        <div className="bg-white rounded-md border border-slate-200/80 shadow-2xs p-5">
                            <div className="flex items-center gap-2 mb-4 pb-2 border-b border-slate-100">
                                <Settings size={16} className="text-[#0a66c2]" />
                                <h2 className="text-xs font-bold text-slate-800">
                                    Plan Settings
                                </h2>
                            </div>

                            <div className="space-y-4">
                                {/* Button Text */}
                                <div className="space-y-1">
                                    <label className="block text-xs font-bold text-slate-700">
                                        Action Button Text
                                    </label>
                                    <input
                                        type="text"
                                        value={data.button_text}
                                        onChange={(e) => setData("button_text", e.target.value)}
                                        className="w-full h-8 px-3 border border-slate-200 rounded-md text-xs outline-none focus:border-[#0a66c2]"
                                    />
                                    {errors.button_text && (
                                        <p className="text-rose-500 text-[11px] mt-0.5">
                                            {errors.button_text}
                                        </p>
                                    )}
                                </div>

                                {/* Mark as Popular */}
                                <div className="flex items-center justify-between p-2.5 bg-slate-50 rounded-md border border-slate-200">
                                    <span className="text-xs font-semibold text-slate-800">
                                        Mark as Popular
                                    </span>
                                    <label className="relative inline-flex items-center cursor-pointer">
                                        <input
                                            type="checkbox"
                                            checked={data.is_popular}
                                            onChange={(e) => setData("is_popular", e.target.checked)}
                                            className="sr-only peer"
                                        />
                                        <div className="w-9 h-5 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#0a66c2]"></div>
                                    </label>
                                </div>

                                {/* Status Active */}
                                <div className="flex items-center justify-between p-2.5 bg-slate-50 rounded-md border border-slate-200">
                                    <span className="text-xs font-semibold text-slate-800">
                                        Status (Active)
                                    </span>
                                    <label className="relative inline-flex items-center cursor-pointer">
                                        <input
                                            type="checkbox"
                                            checked={data.status}
                                            onChange={(e) => setData("status", e.target.checked)}
                                            className="sr-only peer"
                                        />
                                        <div className="w-9 h-5 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#0a66c2]"></div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {/* Save Actions */}
                        <div className="bg-white rounded-md border border-slate-200/80 shadow-2xs p-4 space-y-2">
                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full h-9 bg-[#0a66c2] hover:bg-[#084e96] text-white rounded-md text-xs font-semibold transition-all flex items-center justify-center gap-1.5 shadow-2xs disabled:opacity-50"
                            >
                                {processing ? "Saving..." : "Update Pricing Plan"}
                            </button>
                            <Link
                                href={route("admin.pricing-plans.index")}
                                className="w-full h-8 flex items-center justify-center text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors"
                            >
                                Cancel
                            </Link>
                        </div>
                    </div>
                </form>
            </div>
        </AdminLayout>
    );
}
