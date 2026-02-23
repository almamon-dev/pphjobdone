import React, { useState } from "react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Head, Link, useForm } from "@inertiajs/react";
import {
    Home,
    DollarSign,
    Briefcase,
    Plus,
    Trash2,
    MinusCircle,
    CheckCircle2,
    Settings,
} from "lucide-react";

export default function Create() {
    const { data, setData, post, processing, errors } = useForm({
        name: "",
        price: "",
        subtitle: "",
        is_popular: false,
        features: [""],
        button_text: "Get Started",
        status: true,
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

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route("admin.pricing-plans.store"));
    };

    return (
        <AdminLayout>
            <Head title="Create Pricing Plan" />

            <div className="space-y-4 max-w-[1000px] mx-auto pb-20">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <h1 className="text-[24px] font-bold text-[#2f3344] tracking-tight">
                            Add New Pricing Plan
                        </h1>
                        <div className="flex items-center gap-2 text-[13px] text-[#727586] mt-1">
                            <Home size={16} className="text-[#727586]" />
                            <span className="text-[#c3c4ca]">-</span>
                            <Link
                                href={route("admin.pricing-plans.index")}
                                className="hover:text-[#673ab7] transition-colors"
                            >
                                Pricing Plans
                            </Link>
                            <span className="text-[#c3c4ca]">-</span>
                            <span>Create</span>
                        </div>
                    </div>
                </div>

                <form
                    onSubmit={handleSubmit}
                    className="grid grid-cols-12 gap-5 items-start"
                >
                    {/* Left Column: Basic Info & Features */}
                    <div className="col-span-12 lg:col-span-8 space-y-5">
                        <div className="bg-white rounded-[10px] border border-[#e3e4e8] shadow-sm p-6">
                            <div className="flex items-center gap-2.5 mb-6">
                                <DollarSign
                                    size={20}
                                    className="text-[#673ab7]"
                                />
                                <h2 className="text-[16px] font-bold text-[#2f3344]">
                                    Plan Details
                                </h2>
                            </div>

                            <div className="space-y-4">
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div className="space-y-1.5">
                                        <label className="block text-[13px] font-bold text-[#2f3344]">
                                            Plan Name{" "}
                                            <span className="text-red-500">
                                                *
                                            </span>
                                        </label>
                                        <input
                                            type="text"
                                            value={data.name}
                                            onChange={(e) =>
                                                setData("name", e.target.value)
                                            }
                                            placeholder="e.g., Enterprise Plan"
                                            className={`w-full h-[44px] px-4 border ${errors.name ? "border-red-500" : "border-[#e3e4e8]"} rounded-[8px] focus:ring-1 focus:ring-[#673ab7] outline-none transition-all text-[14px]`}
                                        />
                                        {errors.name && (
                                            <p className="text-red-500 text-[11px] mt-1">
                                                {errors.name}
                                            </p>
                                        )}
                                    </div>

                                    <div className="space-y-1.5">
                                        <label className="block text-[13px] font-bold text-[#2f3344]">
                                            Price{" "}
                                            <span className="text-red-500">
                                                *
                                            </span>
                                        </label>
                                        <input
                                            type="text"
                                            value={data.price}
                                            onChange={(e) =>
                                                setData("price", e.target.value)
                                            }
                                            placeholder="e.g., $19.99/mo"
                                            className={`w-full h-[44px] px-4 border ${errors.price ? "border-red-500" : "border-[#e3e4e8]"} rounded-[8px] focus:ring-1 focus:ring-[#673ab7] outline-none transition-all text-[14px]`}
                                        />
                                        {errors.price && (
                                            <p className="text-red-500 text-[11px] mt-1">
                                                {errors.price}
                                            </p>
                                        )}
                                    </div>
                                </div>

                                <div className="space-y-1.5">
                                    <label className="block text-[13px] font-bold text-[#2f3344]">
                                        Subtitle
                                    </label>
                                    <input
                                        type="text"
                                        value={data.subtitle}
                                        onChange={(e) =>
                                            setData("subtitle", e.target.value)
                                        }
                                        placeholder="e.g., Best for growing businesses"
                                        className="w-full h-[44px] px-4 border border-[#e3e4e8] rounded-[8px] focus:ring-1 focus:ring-[#673ab7] outline-none text-[14px]"
                                    />
                                    {errors.subtitle && (
                                        <p className="text-red-500 text-[11px] mt-1">
                                            {errors.subtitle}
                                        </p>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Features Section */}
                        <div className="bg-white rounded-[10px] border border-[#e3e4e8] shadow-sm p-6">
                            <div className="flex items-center justify-between mb-6">
                                <div className="flex items-center gap-2.5">
                                    <CheckCircle2
                                        size={20}
                                        className="text-[#673ab7]"
                                    />
                                    <h2 className="text-[16px] font-bold text-[#2f3344]">
                                        Plan Features
                                    </h2>
                                </div>
                                <button
                                    type="button"
                                    onClick={addFeature}
                                    className="text-[#673ab7] text-[13px] font-bold hover:underline flex items-center gap-1"
                                >
                                    <Plus size={16} /> Add Feature
                                </button>
                            </div>

                            <div className="space-y-3">
                                {data.features.map((feature, index) => (
                                    <div key={index} className="flex gap-2">
                                        <input
                                            type="text"
                                            value={feature}
                                            onChange={(e) =>
                                                updateFeature(
                                                    index,
                                                    e.target.value,
                                                )
                                            }
                                            placeholder="e.g., 24/7 Support"
                                            className="flex-1 h-[40px] px-4 border border-[#e3e4e8] rounded-lg text-[14px] focus:border-[#673ab7] outline-none"
                                        />
                                        {data.features.length > 1 && (
                                            <button
                                                type="button"
                                                onClick={() =>
                                                    removeFeature(index)
                                                }
                                                className="text-red-400 hover:text-red-600 px-1"
                                            >
                                                <Trash2 size={18} />
                                            </button>
                                        )}
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* Right Column: Settings & Publish */}
                    <div className="col-span-12 lg:col-span-4 space-y-5">
                        <div className="bg-white rounded-[10px] border border-[#e3e4e8] shadow-sm p-6">
                            <div className="flex items-center gap-2.5 mb-6">
                                <Settings
                                    size={20}
                                    className="text-[#673ab7]"
                                />
                                <h2 className="text-[16px] font-bold text-[#2f3344]">
                                    Settings
                                </h2>
                            </div>

                            <div className="space-y-5">
                                <div className="space-y-1.5">
                                    <label className="block text-[13px] font-bold text-[#2f3344]">
                                        Button Text
                                    </label>
                                    <input
                                        type="text"
                                        value={data.button_text}
                                        onChange={(e) =>
                                            setData(
                                                "button_text",
                                                e.target.value,
                                            )
                                        }
                                        className="w-full h-[40px] px-3 border border-[#e3e4e8] rounded-lg outline-none text-[14px]"
                                    />
                                    {errors.button_text && (
                                        <p className="text-red-500 text-[11px] mt-1">
                                            {errors.button_text}
                                        </p>
                                    )}
                                </div>

                                <div className="flex items-center justify-between p-3 bg-[#f8f9fc] rounded-lg border border-[#e3e4e8]">
                                    <span className="text-[13px] font-bold text-[#2f3344]">
                                        Mark as Popular
                                    </span>
                                    <label className="relative inline-flex items-center cursor-pointer">
                                        <input
                                            type="checkbox"
                                            checked={data.is_popular}
                                            onChange={(e) =>
                                                setData(
                                                    "is_popular",
                                                    e.target.checked,
                                                )
                                            }
                                            className="sr-only peer"
                                        />
                                        <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#673ab7]"></div>
                                    </label>
                                </div>

                                <div className="flex items-center justify-between p-3 bg-[#f8f9fc] rounded-lg border border-[#e3e4e8]">
                                    <span className="text-[13px] font-bold text-[#2f3344]">
                                        Status (Active)
                                    </span>
                                    <label className="relative inline-flex items-center cursor-pointer">
                                        <input
                                            type="checkbox"
                                            checked={data.status}
                                            onChange={(e) =>
                                                setData(
                                                    "status",
                                                    e.target.checked,
                                                )
                                            }
                                            className="sr-only peer"
                                        />
                                        <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#673ab7]"></div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div className="bg-white rounded-[10px] border border-[#e3e4e8] shadow-sm p-6">
                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full h-[46px] bg-[#673ab7] text-white rounded-lg text-[14px] font-bold hover:bg-[#5e35b1] transition-all flex items-center justify-center gap-2 disabled:opacity-50"
                            >
                                {processing
                                    ? "Creating..."
                                    : "Create Pricing Plan"}
                            </button>
                            <Link
                                href={route("admin.pricing-plans.index")}
                                className="w-full h-[46px] mt-3 flex items-center justify-center text-[13px] font-bold text-[#727586] hover:text-[#2f3344] transition-colors"
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
