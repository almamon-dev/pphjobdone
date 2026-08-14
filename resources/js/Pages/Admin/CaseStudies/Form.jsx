import React, { useState } from "react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Head, useForm, Link } from "@inertiajs/react";
import { ArrowLeft, Save, Upload, Layers } from "lucide-react";

export default function Form({ caseStudy = null }) {
    const isEditing = !!caseStudy;

    const { data, setData, post, put, errors, processing } = useForm({
        category: caseStudy?.category || "E-commerce",
        title: caseStudy?.title || "",
        stats: caseStudy?.stats || "",
        image: caseStudy?.image || "",
        image_file: null,
        client_type: caseStudy?.client_type || "",
        location: caseStudy?.location || "",
        service_provided: caseStudy?.service_provided || "",
        duration: caseStudy?.duration || "6 Months",
        challenge_description: caseStudy?.challenge_description || "",
        approach_description: caseStudy?.approach_description || "",
        results_description: caseStudy?.results_description || "",
        is_active: caseStudy?.is_active ?? true,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        if (isEditing) {
            put(route("admin.case-studies.update", caseStudy.id));
        } else {
            post(route("admin.case-studies.store"));
        }
    };

    return (
        <AdminLayout>
            <Head title={isEditing ? "Edit Case Study" : "Create Case Study"} />

            <div className="space-y-5 max-w-[1200px] mx-auto pb-12">
                {/* HEADER */}
                <div className="bg-white rounded-md p-5 border border-slate-200/80 shadow-2xs flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <Link
                            href={route("admin.case-studies.index")}
                            className="w-9 h-9 rounded-md border border-slate-200 text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-all"
                        >
                            <ArrowLeft size={18} />
                        </Link>
                        <div>
                            <h1 className="text-xl font-bold text-slate-900 leading-tight">
                                {isEditing ? "Edit Case Study" : "Create New Case Study"}
                            </h1>
                            <p className="text-sm text-slate-600 mt-0.5">
                                Configure project portfolio data, highlighted stats, and client information.
                            </p>
                        </div>
                    </div>
                </div>

                {/* FORM */}
                <form onSubmit={handleSubmit} className="bg-white rounded-md border border-slate-200/80 shadow-2xs p-6 space-y-6">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {/* Title */}
                        <div>
                            <label className="block text-xs font-bold text-slate-700 uppercase mb-2">
                                Project Subtitle / Services Title *
                            </label>
                            <input
                                type="text"
                                value={data.title}
                                onChange={(e) => setData("title", e.target.value)}
                                placeholder="e.g. SEO Monthly + Content Writing"
                                className="w-full h-10 px-3 bg-slate-50 border border-slate-200 rounded-md text-sm font-medium focus:outline-none focus:border-[#0a66c2]"
                                required
                            />
                            {errors.title && <p className="text-xs text-rose-600 mt-1">{errors.title}</p>}
                        </div>

                        {/* Highlighted Stat */}
                        <div>
                            <label className="block text-xs font-bold text-slate-700 uppercase mb-2">
                                Highlighted Stat / Main Result *
                            </label>
                            <input
                                type="text"
                                value={data.stats}
                                onChange={(e) => setData("stats", e.target.value)}
                                placeholder="e.g. +120% Organic Traffic in 6 Months"
                                className="w-full h-10 px-3 bg-slate-50 border border-slate-200 rounded-md text-sm font-medium focus:outline-none focus:border-[#0a66c2]"
                                required
                            />
                            {errors.stats && <p className="text-xs text-rose-600 mt-1">{errors.stats}</p>}
                        </div>

                        {/* Category */}
                        <div>
                            <label className="block text-xs font-bold text-slate-700 uppercase mb-2">
                                Industry Category *
                            </label>
                            <input
                                type="text"
                                value={data.category}
                                onChange={(e) => setData("category", e.target.value)}
                                placeholder="e.g. E-commerce, SaaS, Real Estate, Healthcare"
                                className="w-full h-10 px-3 bg-slate-50 border border-slate-200 rounded-md text-sm font-medium focus:outline-none focus:border-[#0a66c2]"
                                required
                            />
                            {errors.category && <p className="text-xs text-rose-600 mt-1">{errors.category}</p>}
                        </div>

                        {/* Image Path or File */}
                        <div>
                            <label className="block text-xs font-bold text-slate-700 uppercase mb-2">
                                Thumbnail Image URL / Path
                            </label>
                            <input
                                type="text"
                                value={data.image}
                                onChange={(e) => setData("image", e.target.value)}
                                placeholder="/storage/case-studies/p1.webp"
                                className="w-full h-10 px-3 bg-slate-50 border border-slate-200 rounded-md text-sm font-medium focus:outline-none focus:border-[#0a66c2]"
                            />
                        </div>

                        {/* Client Type */}
                        <div>
                            <label className="block text-xs font-bold text-slate-700 uppercase mb-2">
                                Client Type
                            </label>
                            <input
                                type="text"
                                value={data.client_type}
                                onChange={(e) => setData("client_type", e.target.value)}
                                placeholder="e.g. Online Retailer, Software Company"
                                className="w-full h-10 px-3 bg-slate-50 border border-slate-200 rounded-md text-sm font-medium focus:outline-none focus:border-[#0a66c2]"
                            />
                        </div>

                        {/* Location */}
                        <div>
                            <label className="block text-xs font-bold text-slate-700 uppercase mb-2">
                                Location
                            </label>
                            <input
                                type="text"
                                value={data.location}
                                onChange={(e) => setData("location", e.target.value)}
                                placeholder="e.g. United States, Germany, United Kingdom"
                                className="w-full h-10 px-3 bg-slate-50 border border-slate-200 rounded-md text-sm font-medium focus:outline-none focus:border-[#0a66c2]"
                            />
                        </div>

                        {/* Service Provided */}
                        <div>
                            <label className="block text-xs font-bold text-slate-700 uppercase mb-2">
                                Service Provided Description
                            </label>
                            <input
                                type="text"
                                value={data.service_provided}
                                onChange={(e) => setData("service_provided", e.target.value)}
                                placeholder="e.g. SEO Monthly + Content Writing"
                                className="w-full h-10 px-3 bg-slate-50 border border-slate-200 rounded-md text-sm font-medium focus:outline-none focus:border-[#0a66c2]"
                            />
                        </div>

                        {/* Duration */}
                        <div>
                            <label className="block text-xs font-bold text-slate-700 uppercase mb-2">
                                Duration
                            </label>
                            <input
                                type="text"
                                value={data.duration}
                                onChange={(e) => setData("duration", e.target.value)}
                                placeholder="e.g. 6 Months"
                                className="w-full h-10 px-3 bg-slate-50 border border-slate-200 rounded-md text-sm font-medium focus:outline-none focus:border-[#0a66c2]"
                            />
                        </div>
                    </div>

                    {/* Descriptions */}
                    <div className="space-y-4">
                        <div>
                            <label className="block text-xs font-bold text-slate-700 uppercase mb-2">
                                Challenge Description
                            </label>
                            <textarea
                                rows="3"
                                value={data.challenge_description}
                                onChange={(e) => setData("challenge_description", e.target.value)}
                                className="w-full p-3 bg-slate-50 border border-slate-200 rounded-md text-sm font-medium focus:outline-none focus:border-[#0a66c2]"
                                placeholder="Describe the client's initial challenges..."
                            />
                        </div>

                        <div>
                            <label className="block text-xs font-bold text-slate-700 uppercase mb-2">
                                Approach Description
                            </label>
                            <textarea
                                rows="3"
                                value={data.approach_description}
                                onChange={(e) => setData("approach_description", e.target.value)}
                                className="w-full p-3 bg-slate-50 border border-slate-200 rounded-md text-sm font-medium focus:outline-none focus:border-[#0a66c2]"
                                placeholder="Describe your team's strategy and methodology..."
                            />
                        </div>

                        <div>
                            <label className="block text-xs font-bold text-slate-700 uppercase mb-2">
                                Results Summary
                            </label>
                            <textarea
                                rows="3"
                                value={data.results_description}
                                onChange={(e) => setData("results_description", e.target.value)}
                                className="w-full p-3 bg-slate-50 border border-slate-200 rounded-md text-sm font-medium focus:outline-none focus:border-[#0a66c2]"
                                placeholder="Summarize final growth achievements..."
                            />
                        </div>
                    </div>

                    {/* SUBMIT ACTION */}
                    <div className="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                        <Link
                            href={route("admin.case-studies.index")}
                            className="px-4 py-2 bg-slate-100 text-slate-700 rounded-md font-semibold text-sm hover:bg-slate-200 transition-all"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="px-5 py-2 bg-[#0a66c2] hover:bg-[#084e96] text-white rounded-md font-semibold text-sm transition-all flex items-center gap-2"
                        >
                            <Save size={16} /> {isEditing ? "Update Case Study" : "Save Case Study"}
                        </button>
                    </div>
                </form>
            </div>
        </AdminLayout>
    );
}
