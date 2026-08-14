import React, { useState } from "react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Head, Link, router } from "@inertiajs/react";
import {
    Search,
    Plus,
    Edit3,
    Trash2,
    Briefcase,
    Layers,
    ChevronLeft,
    ChevronRight,
} from "lucide-react";

export default function Index({ caseStudies, filters = {} }) {
    const [search, setSearch] = useState(filters.search || "");

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(
            route("admin.case-studies.index"),
            { search },
            { preserveState: true, replace: true }
        );
    };

    const handleDelete = (id) => {
        if (confirm("Are you sure you want to delete this case study?")) {
            router.delete(route("admin.case-studies.destroy", id));
        }
    };

    return (
        <AdminLayout>
            <Head title="Case Studies Management" />

            <div className="space-y-4 max-w-[1600px] mx-auto pb-12">
                {/* HEADER BANNER */}
                <div className="bg-white rounded-md p-5 border border-slate-200/80 shadow-2xs flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-md bg-[#0a66c2]/10 text-[#0a66c2] flex items-center justify-center border border-[#0a66c2]/20 font-bold shrink-0">
                            <Layers size={22} />
                        </div>
                        <div>
                            <h1 className="text-xl font-bold text-slate-900 leading-tight">
                                Case Studies Management
                            </h1>
                            <p className="text-sm text-slate-600 mt-0.5">
                                Manage portfolio projects, client success stories, stats, and approach checklists.
                            </p>
                        </div>
                    </div>

                    <Link
                        href={route("admin.case-studies.create")}
                        className="px-4 py-2 bg-[#0a66c2] hover:bg-[#084e96] text-white rounded-md font-semibold text-sm transition-all flex items-center gap-2 shadow-2xs"
                    >
                        <Plus size={18} /> Add Case Study
                    </Link>
                </div>

                {/* TABLE CONTAINER */}
                <div className="bg-white rounded-md border border-slate-200/80 shadow-2xs overflow-hidden">
                    {/* SEARCH & FILTERS */}
                    <div className="p-4 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between">
                        <form onSubmit={handleSearch} className="relative w-full sm:w-[350px]">
                            <Search size={17} className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
                            <input
                                type="text"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Search case study by title or category..."
                                className="w-full h-10 pl-10 pr-4 bg-white border border-slate-200 rounded-md text-sm font-medium focus:outline-none focus:border-[#0a66c2] text-slate-800"
                            />
                        </form>

                        <div className="text-xs font-semibold text-slate-600">
                            Total Items: {caseStudies.total || 0}
                        </div>
                    </div>

                    {/* TABLE */}
                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse">
                            <thead>
                                <tr className="border-b border-slate-200 bg-slate-50 text-xs font-bold text-slate-700">
                                    <th className="px-5 py-3">Project / Image</th>
                                    <th className="px-5 py-3">Category</th>
                                    <th className="px-5 py-3">Highlighted Stat</th>
                                    <th className="px-5 py-3">Client & Location</th>
                                    <th className="px-5 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 text-sm">
                                {caseStudies.data && caseStudies.data.length > 0 ? (
                                    caseStudies.data.map((study) => (
                                        <tr key={study.id} className="hover:bg-slate-50/80 transition-colors">
                                            {/* Image & Title */}
                                            <td className="px-5 py-3.5 align-top">
                                                <div className="flex items-center gap-3">
                                                    <img
                                                        src={study.image}
                                                        alt={study.title}
                                                        className="w-14 h-10 object-cover rounded-md border border-slate-200 shrink-0"
                                                    />
                                                    <div>
                                                        <p className="font-bold text-slate-900 text-sm">
                                                            {study.title}
                                                        </p>
                                                        <p className="text-xs text-slate-500">
                                                            Duration: {study.duration || "N/A"}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>

                                            {/* Category */}
                                            <td className="px-5 py-3.5 align-top">
                                                <span className="px-2.5 py-1 bg-slate-100 text-slate-800 text-xs font-bold rounded-full border border-slate-200">
                                                    {study.category}
                                                </span>
                                            </td>

                                            {/* Stats */}
                                            <td className="px-5 py-3.5 align-top">
                                                <span className="font-bold text-emerald-700 text-xs bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-200">
                                                    {study.stats}
                                                </span>
                                            </td>

                                            {/* Client & Location */}
                                            <td className="px-5 py-3.5 align-top text-xs text-slate-600 font-medium">
                                                <div>{study.client_type || "N/A"}</div>
                                                <div className="text-slate-400">{study.location || "N/A"}</div>
                                            </td>

                                            {/* Actions */}
                                            <td className="px-5 py-3.5 text-right align-top">
                                                <div className="flex items-center justify-end gap-2">
                                                    <Link
                                                        href={route("admin.case-studies.edit", study.id)}
                                                        className="w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 text-slate-700 hover:bg-slate-100 transition-all"
                                                        title="Edit"
                                                    >
                                                        <Edit3 size={15} />
                                                    </Link>
                                                    <button
                                                        onClick={() => handleDelete(study.id)}
                                                        className="w-8 h-8 flex items-center justify-center rounded-md border border-slate-200 text-rose-600 hover:bg-rose-50 transition-all"
                                                        title="Delete"
                                                    >
                                                        <Trash2 size={15} />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="5" className="px-5 py-12 text-center text-slate-400 text-sm">
                                            No Case Studies found in database.
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
