import React, { useState } from "react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Head, Link, useForm } from "@inertiajs/react";
import {
    Home,
    Briefcase,
    Upload,
    Trash2,
    CheckCircle2,
    Plus,
    HelpCircle,
    MinusCircle,
    Clock,
    DollarSign,
    TrendingUp,
    BarChart3,
    Zap,
} from "lucide-react";

export default function Edit({ service, pricing_plans = [] }) {
    const { data, setData, post, processing, errors } = useForm({
        pricing_plan_ids: service.pricing_plans?.map(p => p.id) || [],
        title: service.title || "",
        subtitle: service.subtitle || "",
        video_source:
            service.video_url &&
                (service.video_url.startsWith("http") ||
                    service.video_url.startsWith("https"))
                ? "url"
                : "upload",
        video_url: service.video_url || "",
        video_file: null,
        thumbnail: null,
        faqs: service.faqs || [{ question: "", answer: "" }],
        service_features: service.service_features?.map((b) => ({
            ...b,
            points: b.points ?? [],
        })) || [{ title: "", description: "", icon: null, points: [] }],
        section_one: service.section_one || {
            title: "",
            subtitle: "",
            description: "",
            points: [""],
            button_text: "",
        },
        section_two: service.section_two || {
            title: "",
            subtitle: "",
            description: "",
            points: [""],
            button_text: "",
        },
        section_one_image: null,
        section_two_image: null,
        status: service.status ? true : false,
        is_campaign: service.is_campaign ? true : false,
        has_faq: service.has_faq ? true : false,
        has_secondary_features: service.has_secondary_features ? true : false,
        has_benifite: service.has_benifite !== undefined ? (service.has_benifite ? true : false) : true,
        has_why_chose_us: service.has_why_chose_us !== undefined ? (service.has_why_chose_us ? true : false) : true,
        has_brands: service.has_brands !== undefined ? (service.has_brands ? true : false) : true,
        has_expect_result: service.has_expect_result !== undefined ? (service.has_expect_result ? true : false) : true,
        secondary_features: service.secondary_features || [{ title: "", description: "", icon: null }],
        expect_results: service.expect_results || [{ title: "", value: "", subtitle: "", icon: "ArrowUpRight" }],
        brands: service.brands || [{ name: "", logo: null }],
        _method: "PUT",
    });

    const [thumbPreview, setThumbPreview] = useState(
        service.thumbnail ? `/${service.thumbnail}` : null,
    );
    const [sectionOnePreview, setSectionOnePreview] = useState(
        service.section_one?.image ? `/${service.section_one.image}` : null,
    );
    const [sectionTwoPreview, setSectionTwoPreview] = useState(
        service.section_two?.image ? `/${service.section_two.image}` : null,
    );

    const handleFileChange = (e, field) => {
        const file = e.target.files[0];
        if (file) {
            setData(field, file);
            if (field === "thumbnail")
                setThumbPreview(URL.createObjectURL(file));
            if (field === "section_one_image")
                setSectionOnePreview(URL.createObjectURL(file));
            if (field === "section_two_image")
                setSectionTwoPreview(URL.createObjectURL(file));
            if (field === "video_file") {
                setData((prev) => ({ ...prev, video_url: "" })); // Clear URL if file is uploaded
            }
        }
    };

    const handlePlanToggle = (id) => {
        const currentIds = [...data.pricing_plan_ids];
        if (currentIds.includes(id)) {
            setData(
                "pricing_plan_ids",
                currentIds.filter((i) => i !== id),
            );
        } else {
            setData("pricing_plan_ids", [...currentIds, id]);
        }
    };



    const updateSection = (sectionName, field, value) => {
        setData(sectionName, {
            ...data[sectionName],
            [field]: value,
        });
    };

    const addSectionPoint = (sectionName) => {
        const newSection = { ...data[sectionName] };
        newSection.points = [...(newSection.points || []), ""];
        setData(sectionName, newSection);
    };

    const removeSectionPoint = (sectionName, index) => {
        const newSection = { ...data[sectionName] };
        newSection.points = newSection.points.filter((_, i) => i !== index);
        setData(sectionName, newSection);
    };

    const updateSectionPoint = (sectionName, index, value) => {
        const newSection = { ...data[sectionName] };
        newSection.points[index] = value;
        setData(sectionName, newSection);
    };

    const addBenefit = () => {
        setData("service_features", [
            ...data.service_features,
            { title: "", description: "", icon: null, points: [] },
        ]);
    };

    const removeBenefit = (index) => {
        const newBenefits = data.service_features.filter((_, i) => i !== index);
        setData("service_features", newBenefits);
    };

    const updateBenefit = (index, field, value) => {
        const newBenefits = [...data.service_features];
        newBenefits[index][field] = value;
        setData("service_features", newBenefits);
    };

    const addBenefitPoint = (benefitIndex) => {
        const updated = [...data.service_features];
        updated[benefitIndex].points = [...(updated[benefitIndex].points || []), ""];
        setData("service_features", updated);
    };

    const removeBenefitPoint = (benefitIndex, pointIndex) => {
        const updated = [...data.service_features];
        updated[benefitIndex].points = updated[benefitIndex].points.filter((_, i) => i !== pointIndex);
        setData("service_features", updated);
    };

    const updateBenefitPoint = (benefitIndex, pointIndex, value) => {
        const updated = [...data.service_features];
        updated[benefitIndex].points[pointIndex] = value;
        setData("service_features", updated);
    };

    const addSecondaryFeature = () => {
        setData("secondary_features", [
            ...(data.secondary_features || []),
            { title: "", description: "", icon: null },
        ]);
    };

    const removeSecondaryFeature = (index) => {
        const newFeatures = data.secondary_features.filter((_, i) => i !== index);
        setData("secondary_features", newFeatures);
    };

    const updateSecondaryFeature = (index, field, value) => {
        const newFeatures = [...data.secondary_features];
        newFeatures[index][field] = value;
        setData("secondary_features", newFeatures);
    };

    const addFaq = () => {
        setData("faqs", [...data.faqs, { question: "", answer: "" }]);
    };

    const removeFaq = (index) => {
        const newFaqs = data.faqs.filter((_, i) => i !== index);
        setData("faqs", newFaqs);
    };

    const updateFaq = (index, field, value) => {
        const newFaqs = [...data.faqs];
        newFaqs[index][field] = value;
        setData("faqs", newFaqs);
    };

    const addExpectResult = () => {
        setData("expect_results", [
            ...(data.expect_results || []),
            { title: "", value: "", subtitle: "", icon: "ArrowUpRight" }
        ]);
    };

    const removeExpectResult = (index) => {
        const newResults = data.expect_results.filter((_, i) => i !== index);
        setData("expect_results", newResults);
    };

    const updateExpectResult = (index, field, value) => {
        const newResults = [...data.expect_results];
        newResults[index][field] = value;
        setData("expect_results", newResults);
    };

    const addBrand = () => {
        setData("brands", [
            ...(data.brands || []),
            { name: "", logo: null }
        ]);
    };

    const removeBrand = (index) => {
        const newBrands = data.brands.filter((_, i) => i !== index);
        setData("brands", newBrands);
    };

    const updateBrand = (index, field, value) => {
        const newBrands = [...data.brands];
        newBrands[index][field] = value;
        setData("brands", newBrands);
    };





    const handleSubmit = (e) => {
        e.preventDefault();
        post(route("admin.services.update", service.id));
    };

    return (
        <AdminLayout>
            <Head title="Edit Service" />

            <div className="space-y-4 max-w-full mx-auto pb-20">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <h1 className="text-[24px] font-bold text-[#2f3344] tracking-tight">
                            Edit Service
                        </h1>
                        <div className="flex items-center gap-2 text-[13px] text-[#727586] mt-1">
                            <Home size={16} className="text-[#727586]" />
                            <span className="text-[#c3c4ca]">-</span>
                            <Link
                                href={route("admin.services.index")}
                                className="hover:text-[#673ab7] transition-colors"
                            >
                                Services
                            </Link>
                            <span className="text-[#c3c4ca]">-</span>
                            <span>Edit</span>
                        </div>
                    </div>
                </div>

                <form
                    onSubmit={handleSubmit}
                    className="grid grid-cols-12 gap-5 items-start"
                >
                    {/* Left Column: Main Content */}
                    <div className="col-span-12 lg:col-span-8 space-y-5">
                        {/* Basic Info */}
                        <div className="bg-white rounded-[10px] border border-[#e3e4e8] shadow-sm p-5">
                            <div className="flex items-center gap-2.5 mb-4">
                                <Briefcase
                                    size={20}
                                    className="text-[#673ab7]"
                                />
                                <h2 className="text-[16px] font-bold text-[#2f3344]">
                                    Basic Information
                                </h2>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div className="space-y-1.5">
                                    <label className="block text-[13px] font-bold text-[#2f3344]">
                                        Service Title{" "}
                                        <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        value={data.title}
                                        onChange={(e) =>
                                            setData("title", e.target.value)
                                        }
                                        placeholder="e.g., Monthly SEO"
                                        className={`w-full h-[44px] px-4 border ${errors.title ? "border-red-500" : "border-[#e3e4e8]"} rounded-[8px] focus:ring-1 focus:ring-[#673ab7] outline-none transition-all text-[14px]`}
                                    />
                                    {errors.title && (
                                        <p className="text-red-500 text-[11px] mt-1">
                                            {errors.title}
                                        </p>
                                    )}
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
                                        placeholder="e.g., Drive Organic Traffic"
                                        className="w-full h-[44px] px-4 border border-[#e3e4e8] rounded-[8px] focus:ring-1 focus:ring-[#673ab7] outline-none text-[14px]"
                                    />
                                </div>
                            </div>
                        </div>

                        {/* Pricing Plans Section */}
                        <div className="bg-white rounded-[10px] border border-[#e3e4e8] shadow-sm p-5">
                            <div className="flex items-center gap-2.5 mb-4">
                                <DollarSign
                                    size={20}
                                    className="text-[#673ab7]"
                                />
                                <h2 className="text-[16px] font-bold text-[#2f3344]">
                                    Associate Pricing Plans
                                </h2>
                            </div>

                            <div className="space-y-1.5">
                                <label className="block text-[12px] font-bold text-[#727586] mb-2">
                                    Select the pricing plans that apply to this service
                                </label>
                                {pricing_plans.length > 0 ? (
                                    <div className="flex flex-wrap gap-x-6 gap-y-3 p-4 border border-[#e3e4e8] rounded-lg bg-[#fcfcfd]">
                                        {pricing_plans.map((plan) => (
                                            <label
                                                key={plan.id}
                                                className="flex items-center gap-2.5 cursor-pointer group whitespace-nowrap"
                                            >
                                                <input
                                                    type="checkbox"
                                                    checked={data.pricing_plan_ids.includes(plan.id)}
                                                    onChange={() => handlePlanToggle(plan.id)}
                                                    className="w-4 h-4 rounded border-gray-300 text-[#673ab7] focus:ring-[#673ab7] flex-shrink-0"
                                                />
                                                <span className="text-[13px] text-[#2f3344] group-hover:text-[#673ab7] transition-colors">
                                                    {plan.name} ({plan.price})
                                                </span>
                                            </label>
                                        ))}
                                    </div>
                                ) : (
                                    <p className="text-[13px] text-[#727586] italic p-3 border border-[#e3e4e8] rounded-lg bg-[#f8f9fc]">
                                        No pricing plans found. Please{" "}
                                        <Link href={route("admin.pricing-plans.create")} className="text-[#673ab7] font-semibold hover:underline">
                                            create a pricing plan
                                        </Link>{" "}
                                        first.
                                    </p>
                                )}
                                {errors.pricing_plan_ids && (
                                    <p className="text-red-500 text-[11px] mt-1">
                                        {errors.pricing_plan_ids}
                                    </p>
                                )}
                            </div>
                        </div>

                        {/* Benefits Section */}
                        <div className="bg-white rounded-[10px] border border-[#e3e4e8] shadow-sm p-4 md:p-5">
                            <div className="flex items-center justify-between mb-4">
                                <div className="flex items-center gap-2.5">
                                    <CheckCircle2
                                        size={20}
                                        className="text-[#673ab7]"
                                    />
                                    <h2 className="text-[16px] font-bold text-[#2f3344]">
                                        Service Features
                                    </h2>
                                </div>
                                <button
                                    type="button"
                                    onClick={addBenefit}
                                    className="text-[#673ab7] text-[12px] font-bold hover:underline flex items-center gap-1"
                                >
                                    <Plus size={14} /> Add Feature
                                </button>
                            </div>

                            <div className="space-y-4">
                                {data.service_features.map((benefit, index) => (
                                    <div
                                        key={index}
                                        className="p-3 bg-[#f8f9fc] rounded-lg border border-[#e3e4e8] space-y-2"
                                    >
                                        <div className="flex justify-between items-start">
                                            <h3 className="text-[13px] font-bold text-[#673ab7]">
                                                Feature {index + 1}
                                            </h3>
                                            {data.service_features.length > 1 && (
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        removeBenefit(index)
                                                    }
                                                    className="text-red-400 hover:text-red-600 transition-colors"
                                                >
                                                    <Trash2 size={16} />
                                                </button>
                                            )}
                                        </div>
                                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                            <div className="md:col-span-2 space-y-4">
                                                <div className="space-y-1">
                                                    <label className="text-[12px] font-bold text-[#727586]">
                                                        Feature Title
                                                    </label>
                                                    <input
                                                        type="text"
                                                        value={benefit.title}
                                                        onChange={(e) =>
                                                            updateBenefit(
                                                                index,
                                                                "title",
                                                                e.target.value,
                                                            )
                                                        }
                                                        placeholder="e.g., Competitor Analysis"
                                                        className="w-full h-[40px] px-3 border border-[#e3e4e8] rounded-lg outline-none font-bold"
                                                    />
                                                </div>
                                                <div className="space-y-1">
                                                    <label className="text-[12px] font-bold text-[#727586]">
                                                        Feature Description / Text
                                                    </label>
                                                    <textarea
                                                        value={benefit.description}
                                                        onChange={(e) =>
                                                            updateBenefit(
                                                                index,
                                                                "description",
                                                                e.target.value,
                                                            )
                                                        }
                                                        placeholder="e.g., It's vital to keep tabs on what the competitor is up to. How else will you..."
                                                        className="w-full min-h-[64px] h-[64px] p-3 border border-[#e3e4e8] rounded-lg outline-none text-[13px] resize-none"
                                                    />
                                                </div>
                                                {/* Bullet Points */}
                                                <div className="space-y-2">
                                                    <div className="flex items-center justify-between">
                                                        <label className="text-[12px] font-bold text-[#727586]">Bullet Points (optional)</label>
                                                        <button
                                                            type="button"
                                                            onClick={() => addBenefitPoint(index)}
                                                            className="text-[#673ab7] text-[11px] font-bold hover:underline flex items-center gap-1"
                                                        >
                                                            <Plus size={12} /> Add Point
                                                        </button>
                                                    </div>
                                                    {(benefit.points || []).map((point, pIdx) => (
                                                        <div key={pIdx} className="flex gap-2">
                                                            <input
                                                                type="text"
                                                                value={point}
                                                                onChange={(e) => updateBenefitPoint(index, pIdx, e.target.value)}
                                                                placeholder={`Point ${pIdx + 1}`}
                                                                className="flex-1 h-[36px] px-3 border border-[#e3e4e8] rounded-lg outline-none text-[12px]"
                                                            />
                                                            <button
                                                                type="button"
                                                                onClick={() => removeBenefitPoint(index, pIdx)}
                                                                className="text-red-400 hover:text-red-600"
                                                            >
                                                                <Trash2 size={14} />
                                                            </button>
                                                        </div>
                                                    ))}
                                                </div>
                                            </div>
                                            <div className="space-y-1">
                                                <label className="text-[12px] font-bold text-[#727586]">
                                                    Feature Icon
                                                </label>
                                                <div className="relative group">
                                                    <input
                                                        type="file"
                                                        onChange={(e) =>
                                                            updateBenefit(
                                                                index,
                                                                "icon",
                                                                e.target
                                                                    .files[0],
                                                            )
                                                        }
                                                        accept="image/*"
                                                        className="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                                    />
                                                    <div className="flex flex-col items-center justify-center w-full h-[110px] bg-white border border-dashed border-[#e3e4e8] rounded-lg group-hover:border-[#673ab7] transition-all overflow-hidden px-3">
                                                        {benefit.icon ? (
                                                            <div className="flex flex-col items-center gap-2">
                                                                <img
                                                                    src={
                                                                        benefit.icon instanceof
                                                                            File
                                                                            ? URL.createObjectURL(
                                                                                benefit.icon,
                                                                            )
                                                                            : `/${benefit.icon}`
                                                                    }
                                                                    alt="Feature Icon"
                                                                    className="h-9 w-9 object-contain"
                                                                />
                                                                <span className="text-[11px] text-[#727586] truncate max-w-[150px]">
                                                                    {benefit.icon instanceof
                                                                        File
                                                                        ? benefit
                                                                            .icon
                                                                            .name
                                                                        : "Current Icon"}
                                                                </span>
                                                            </div>
                                                        ) : (
                                                            <div className="flex flex-col items-center gap-2">
                                                                <Upload
                                                                    size={20}
                                                                    className="text-[#a0a3af]"
                                                                />
                                                                <span className="text-[12px] text-[#727586] text-center">
                                                                    Upload Feature Icon
                                                                </span>
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* Secondary Features Section */}
                        {data.has_secondary_features && (
                            <div className="bg-white rounded-[12px] border border-[#e3e4e8] shadow-sm p-5 md:p-6">
                                <div className="flex items-center justify-between mb-6">
                                    <div className="flex items-center gap-3">
                                        <Zap
                                            size={22}
                                            className="text-[#673ab7]"
                                        />
                                        <h2 className="text-[18px] font-bold text-[#2f3344]">
                                            Secondary Features
                                        </h2>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={addSecondaryFeature}
                                        className="text-[#673ab7] text-[13px] font-bold hover:underline flex items-center gap-1"
                                    >
                                        <Plus size={16} /> Add Secondary Feature
                                    </button>
                                </div>

                                <div className="space-y-4">
                                    {(data.secondary_features || []).map((feature, index) => (
                                        <div
                                            key={index}
                                            className="p-4 bg-[#f8f9fc] rounded-lg border border-[#e3e4e8] space-y-3"
                                        >
                                            <div className="flex justify-between items-start">
                                                <h3 className="text-[14px] font-bold text-[#673ab7]">
                                                    Secondary Feature {index + 1}
                                                </h3>
                                                {(data.secondary_features || []).length > 1 && (
                                                    <button
                                                        type="button"
                                                        onClick={() =>
                                                            removeSecondaryFeature(index)
                                                        }
                                                        className="text-red-500 hover:text-red-700"
                                                    >
                                                        <Trash2 size={16} />
                                                    </button>
                                                )}
                                            </div>
                                            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                                <div className="md:col-span-2 space-y-4">
                                                    <div className="space-y-1">
                                                        <label className="text-[12px] font-bold text-[#727586]">
                                                            Feature Title
                                                        </label>
                                                        <input
                                                            type="text"
                                                            value={feature.title}
                                                            onChange={(e) =>
                                                                updateSecondaryFeature(
                                                                    index,
                                                                    "title",
                                                                    e.target.value,
                                                                )
                                                            }
                                                            placeholder="e.g., Research"
                                                            className="w-full h-[40px] px-3 border border-[#e3e4e8] rounded-lg outline-none font-bold"
                                                        />
                                                    </div>
                                                    <div className="space-y-1">
                                                        <label className="text-[12px] font-bold text-[#727586]">
                                                            Description
                                                        </label>
                                                        <textarea
                                                            value={feature.description}
                                                            onChange={(e) =>
                                                                updateSecondaryFeature(
                                                                    index,
                                                                    "description",
                                                                    e.target.value,
                                                                )
                                                            }
                                                            placeholder="Description..."
                                                            className="w-full min-h-[64px] h-[64px] p-3 border border-[#e3e4e8] rounded-lg outline-none text-[13px] resize-none"
                                                        />
                                                    </div>
                                                </div>
                                                <div className="space-y-1">
                                                    <label className="text-[12px] font-bold text-[#727586]">
                                                        Feature Icon
                                                    </label>
                                                    <div className="relative group">
                                                        <input
                                                            type="file"
                                                            onChange={(e) =>
                                                                updateSecondaryFeature(
                                                                    index,
                                                                    "icon",
                                                                    e.target
                                                                        .files[0],
                                                                )
                                                            }
                                                            accept="image/*"
                                                            className="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                                        />
                                                        <div className="flex flex-col items-center justify-center w-full h-[110px] bg-white border border-dashed border-[#e3e4e8] rounded-lg group-hover:border-[#673ab7] transition-all overflow-hidden px-3">
                                                            {feature.icon ? (
                                                                <div className="flex flex-col items-center gap-2">
                                                                    <img
                                                                        src={
                                                                            feature.icon instanceof
                                                                                File
                                                                                ? URL.createObjectURL(
                                                                                    feature.icon,
                                                                                )
                                                                                : `/${feature.icon}`
                                                                        }
                                                                        alt="Icon"
                                                                        className="h-9 w-9 object-contain"
                                                                    />
                                                                    <span className="text-[11px] text-[#727586] truncate max-w-[150px]">
                                                                        {feature.icon instanceof
                                                                            File
                                                                            ? feature
                                                                                .icon
                                                                                .name
                                                                            : "Current Icon"}
                                                                    </span>
                                                                </div>
                                                            ) : (
                                                                <div className="flex flex-col items-center gap-2">
                                                                    <Upload
                                                                        size={20}
                                                                        className="text-[#a0a3af]"
                                                                    />
                                                                    <span className="text-[12px] text-[#727586] text-center">
                                                                        Upload Feature Icon
                                                                    </span>
                                                                </div>
                                                            )}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}

                        {/* FAQ Section */}
                        {data.has_faq && (
                            <div className="bg-white rounded-[12px] border border-[#e3e4e8] shadow-sm p-5 md:p-6 relative overflow-hidden">
                                <div className="flex items-center justify-between mb-6">
                                    <div className="flex items-center gap-3">
                                        <HelpCircle
                                            size={22}
                                            className="text-[#673ab7]"
                                        />
                                        <h2 className="text-[18px] font-bold text-[#2f3344]">
                                            FAQs
                                        </h2>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={addFaq}
                                        className="text-[#673ab7] text-[13px] font-bold hover:underline flex items-center gap-1"
                                    >
                                        <Plus size={16} /> Add FAQ
                                    </button>
                                </div>

                                <div className="space-y-4">
                                    {data.faqs.map((faq, index) => (
                                        <div
                                            key={index}
                                            className="space-y-2 p-4 bg-[#f8f9fc] rounded-lg border border-[#e3e4e8]"
                                        >
                                            <div className="flex space-x-2">
                                                <input
                                                    type="text"
                                                    value={faq.question}
                                                    onChange={(e) =>
                                                        updateFaq(
                                                            index,
                                                            "question",
                                                            e.target.value,
                                                        )
                                                    }
                                                    placeholder="Question"
                                                    className="flex-1 h-[40px] px-3 border border-[#e3e4e8] rounded-lg outline-none"
                                                />
                                                {data.faqs.length > 1 && (
                                                    <button
                                                        type="button"
                                                        onClick={() =>
                                                            removeFaq(index)
                                                        }
                                                        className="text-red-500"
                                                    >
                                                        <Trash2 size={16} />
                                                    </button>
                                                )}
                                            </div>
                                            <textarea
                                                value={faq.answer}
                                                onChange={(e) =>
                                                    updateFaq(
                                                        index,
                                                        "answer",
                                                        e.target.value,
                                                    )
                                                }
                                                placeholder="Answer"
                                                className="w-full min-h-[80px] p-3 border border-[#e3e4e8] rounded-lg outline-none"
                                            />
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}







                        {/* Content Section One */}
                        {data.has_benifite && (
                            <div className="bg-white rounded-[10px] border border-[#e3e4e8] shadow-sm p-5">
                                <div className="flex items-center gap-2.5 mb-4">
                                    <Briefcase
                                        size={20}
                                        className="text-[#673ab7]"
                                    />
                                    <h2 className="text-[16px] font-bold text-[#2f3344]">
                                        Benefits (Section One)
                                    </h2>
                                </div>

                                <div className="grid grid-cols-1 gap-4">
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div className="space-y-1">
                                            <label className="block text-[13px] font-bold text-[#2f3344]">
                                                Title
                                            </label>
                                            <input
                                                type="text"
                                                value={data.section_one.title}
                                                onChange={(e) =>
                                                    updateSection(
                                                        "section_one",
                                                        "title",
                                                        e.target.value,
                                                    )
                                                }
                                                className="w-full h-[40px] px-3 border border-[#e3e4e8] rounded-lg focus:ring-1 focus:ring-[#673ab7] outline-none text-[13px]"
                                            />
                                        </div>
                                        <div className="space-y-1">
                                            <label className="block text-[13px] font-bold text-[#2f3344]">
                                                Badge (e.g., Our Benefit)
                                            </label>
                                            <input
                                                type="text"
                                                value={data.section_one.subtitle}
                                                onChange={(e) =>
                                                    updateSection(
                                                        "section_one",
                                                        "subtitle",
                                                        e.target.value,
                                                    )
                                                }
                                                placeholder="Our Benefit"
                                                className="w-full h-[40px] px-3 border border-[#e3e4e8] rounded-lg focus:ring-1 focus:ring-[#673ab7] outline-none text-[13px]"
                                            />
                                        </div>
                                    </div>

                                    <div className="space-y-1">
                                        <label className="block text-[13px] font-bold text-[#2f3344]">
                                            Description
                                        </label>
                                        <textarea
                                            value={data.section_one.description}
                                            onChange={(e) =>
                                                updateSection(
                                                    "section_one",
                                                    "description",
                                                    e.target.value,
                                                )
                                            }
                                            placeholder="Brief description..."
                                            className="w-full min-h-[80px] p-3 border border-[#e3e4e8] rounded-lg focus:ring-1 focus:ring-[#673ab7] outline-none text-[13px]"
                                        />
                                    </div>

                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div className="space-y-1">
                                            <label className="block text-[13px] font-bold text-[#2f3344]">
                                                Button Text
                                            </label>
                                            <input
                                                type="text"
                                                value={data.section_one.button_text}
                                                onChange={(e) =>
                                                    updateSection(
                                                        "section_one",
                                                        "button_text",
                                                        e.target.value,
                                                    )
                                                }
                                                placeholder="Contact Us"
                                                className="w-full h-[40px] px-3 border border-[#e3e4e8] rounded-lg focus:ring-1 focus:ring-[#673ab7] outline-none text-[13px]"
                                            />
                                        </div>
                                    </div>

                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div className="space-y-3">
                                            <div className="flex items-center justify-between">
                                                <label className="block text-[13px] font-bold text-[#2f3344]">
                                                    Key Points
                                                </label>
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        addSectionPoint(
                                                            "section_one",
                                                        )
                                                    }
                                                    className="text-[#673ab7] text-[12px] font-bold"
                                                >
                                                    + Add Point
                                                </button>
                                            </div>
                                            <div className="space-y-2">
                                                {data.section_one.points?.map(
                                                    (point, idx) => (
                                                        <div
                                                            key={idx}
                                                            className="flex gap-2"
                                                        >
                                                            <input
                                                                type="text"
                                                                value={point}
                                                                onChange={(e) =>
                                                                    updateSectionPoint(
                                                                        "section_one",
                                                                        idx,
                                                                        e.target
                                                                            .value,
                                                                    )
                                                                }
                                                                className="flex-1 h-[40px] px-3 border border-[#e3e4e8] rounded-lg outline-none text-[13px]"
                                                            />
                                                            <button
                                                                type="button"
                                                                onClick={() =>
                                                                    removeSectionPoint(
                                                                        "section_one",
                                                                        idx,
                                                                    )
                                                                }
                                                                className="text-red-400 hover:text-red-500 transition-colors"
                                                            >
                                                                <Trash2 size={16} />
                                                            </button>
                                                        </div>
                                                    ),
                                                )}
                                            </div>
                                        </div>

                                        <div className="space-y-1">
                                            <label className="block text-[13px] font-bold text-[#2f3344]">
                                                Section Image
                                            </label>
                                            <div className="relative group">
                                                <input
                                                    type="file"
                                                    onChange={(e) =>
                                                        handleFileChange(
                                                            e,
                                                            "section_one_image",
                                                        )
                                                    }
                                                    accept="image/*"
                                                    className="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                                />
                                                <div className="flex flex-col items-center justify-center w-full h-[140px] bg-[#f8f9fc] border-2 border-dashed border-[#e3e4e8] rounded-lg group-hover:border-[#673ab7] transition-all overflow-hidden">
                                                    {sectionOnePreview ||
                                                        service.section_one_image ? (
                                                        <img
                                                            src={
                                                                sectionOnePreview ||
                                                                `/${service.section_one_image}`
                                                            }
                                                            alt="Section Preview"
                                                            className="w-full h-full object-cover"
                                                        />
                                                    ) : (
                                                        <div className="text-center p-2">
                                                            <Upload
                                                                size={20}
                                                                className="text-[#a0a3af] mx-auto mb-1.5"
                                                            />
                                                            <p className="text-[11px] text-[#727586]">
                                                                Upload Image
                                                            </p>
                                                        </div>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Content Section Two */}
                        {data.has_why_chose_us && (
                            <div className="bg-white rounded-[10px] border border-[#e3e4e8] shadow-sm p-5">
                                <div className="flex items-center gap-2.5 mb-4">
                                    <Briefcase
                                        size={20}
                                        className="text-[#673ab7]"
                                    />
                                    <h2 className="text-[16px] font-bold text-[#2f3344]">
                                        Why Us (Section Two)
                                    </h2>
                                </div>

                                <div className="grid grid-cols-1 gap-4">
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div className="space-y-1">
                                            <label className="block text-[13px] font-bold text-[#2f3344]">
                                                Title
                                            </label>
                                            <input
                                                type="text"
                                                value={data.section_two.title}
                                                onChange={(e) =>
                                                    updateSection(
                                                        "section_two",
                                                        "title",
                                                        e.target.value,
                                                    )
                                                }
                                                className="w-full h-[40px] px-3 border border-[#e3e4e8] rounded-lg focus:ring-1 focus:ring-[#673ab7] outline-none text-[13px]"
                                            />
                                        </div>
                                        <div className="space-y-1">
                                            <label className="block text-[13px] font-bold text-[#2f3344]">
                                                Subtitle
                                            </label>
                                            <input
                                                type="text"
                                                value={data.section_two.subtitle}
                                                onChange={(e) =>
                                                    updateSection(
                                                        "section_two",
                                                        "subtitle",
                                                        e.target.value,
                                                    )
                                                }
                                                className="w-full h-[40px] px-3 border border-[#e3e4e8] rounded-lg focus:ring-1 focus:ring-[#673ab7] outline-none text-[13px]"
                                            />
                                        </div>
                                    </div>

                                    <div className="space-y-1">
                                        <label className="block text-[13px] font-bold text-[#2f3344]">
                                            Description
                                        </label>
                                        <textarea
                                            value={data.section_two.description}
                                            onChange={(e) =>
                                                updateSection(
                                                    "section_two",
                                                    "description",
                                                    e.target.value,
                                                )
                                            }
                                            placeholder="Brief description..."
                                            className="w-full min-h-[80px] p-3 border border-[#e3e4e8] rounded-lg focus:ring-1 focus:ring-[#673ab7] outline-none text-[13px]"
                                        />
                                    </div>

                                    <div className="grid grid-cols-1 md:grid-cols-1 gap-4">
                                        <div className="space-y-1">
                                            <label className="block text-[13px] font-bold text-[#2f3344]">
                                                Button Text
                                            </label>
                                            <input
                                                type="text"
                                                value={data.section_two.button_text}
                                                onChange={(e) =>
                                                    updateSection(
                                                        "section_two",
                                                        "button_text",
                                                        e.target.value,
                                                    )
                                                }
                                                placeholder="Learn More"
                                                className="w-full h-[40px] px-3 border border-[#e3e4e8] rounded-lg focus:ring-1 focus:ring-[#673ab7] outline-none text-[13px]"
                                            />
                                        </div>
                                    </div>

                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div className="space-y-3">
                                            <div className="flex items-center justify-between">
                                                <label className="block text-[13px] font-bold text-[#2f3344]">
                                                    Key Points
                                                </label>
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        addSectionPoint(
                                                            "section_two",
                                                        )
                                                    }
                                                    className="text-[#673ab7] text-[12px] font-bold"
                                                >
                                                    + Add Point
                                                </button>
                                            </div>
                                            <div className="space-y-2">
                                                {data.section_two.points?.map(
                                                    (point, idx) => (
                                                        <div
                                                            key={idx}
                                                            className="flex gap-2"
                                                        >
                                                            <input
                                                                type="text"
                                                                value={point}
                                                                onChange={(e) =>
                                                                    updateSectionPoint(
                                                                        "section_two",
                                                                        idx,
                                                                        e.target
                                                                            .value,
                                                                    )
                                                                }
                                                                className="flex-1 h-[40px] px-3 border border-[#e3e4e8] rounded-lg outline-none text-[13px]"
                                                            />
                                                            <button
                                                                type="button"
                                                                onClick={() =>
                                                                    removeSectionPoint(
                                                                        "section_two",
                                                                        idx,
                                                                    )
                                                                }
                                                                className="text-red-400 hover:text-red-500 transition-colors"
                                                            >
                                                                <Trash2 size={16} />
                                                            </button>
                                                        </div>
                                                    ),
                                                )}
                                            </div>
                                        </div>

                                        <div className="space-y-1">
                                            <label className="block text-[13px] font-bold text-[#2f3344]">
                                                Section Image
                                            </label>
                                            <div className="relative group">
                                                <input
                                                    type="file"
                                                    onChange={(e) =>
                                                        handleFileChange(
                                                            e,
                                                            "section_two_image",
                                                        )
                                                    }
                                                    accept="image/*"
                                                    className="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                                />
                                                <div className="flex flex-col items-center justify-center w-full h-[140px] bg-[#f8f9fc] border-2 border-dashed border-[#e3e4e8] rounded-lg group-hover:border-[#673ab7] transition-all overflow-hidden">
                                                    {sectionTwoPreview ||
                                                        service.section_two_image ? (
                                                        <img
                                                            src={
                                                                sectionTwoPreview ||
                                                                `/${service.section_two_image}`
                                                            }
                                                            alt="Section Preview"
                                                            className="w-full h-full object-cover"
                                                        />
                                                    ) : (
                                                        <div className="text-center p-2">
                                                            <Upload
                                                                size={20}
                                                                className="text-[#a0a3af] mx-auto mb-1.5"
                                                            />
                                                            <p className="text-[11px] text-[#727586]">
                                                                Upload Image
                                                            </p>
                                                        </div>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Expected Results Section */}
                        {data.has_expect_result && (
                            <div className="bg-white rounded-[12px] border border-[#e3e4e8] shadow-sm p-5 md:p-6">
                                <div className="flex items-center justify-between mb-6">
                                    <div className="flex items-center gap-3">
                                        <TrendingUp
                                            size={22}
                                            className="text-[#673ab7]"
                                        />
                                        <h2 className="text-[18px] font-bold text-[#2f3344]">
                                            Driving Real Results (Expected Results)
                                        </h2>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={addExpectResult}
                                        className="text-[#673ab7] text-[13px] font-bold hover:underline flex items-center gap-1"
                                    >
                                        <Plus size={16} /> Add Stat Card
                                    </button>
                                </div>

                                <div className="space-y-4">
                                    {(data.expect_results || []).map((result, index) => (
                                        <div
                                            key={index}
                                            className="p-4 bg-[#f8f9fc] rounded-lg border border-[#e3e4e8] space-y-3 relative"
                                        >
                                            <div className="flex justify-between items-start">
                                                <h3 className="text-[14px] font-bold text-[#673ab7]">
                                                    Stat Card {index + 1}
                                                </h3>
                                                {(data.expect_results || []).length > 1 && (
                                                    <button
                                                        type="button"
                                                        onClick={() => removeExpectResult(index)}
                                                        className="text-red-500 hover:text-red-700"
                                                    >
                                                        <Trash2 size={16} />
                                                    </button>
                                                )}
                                            </div>
                                            <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                                                <div className="space-y-1">
                                                    <label className="text-[12px] font-bold text-[#727586]">
                                                        Stat Value (e.g. 100%, 3%, 15m)
                                                    </label>
                                                    <input
                                                        type="text"
                                                        value={result.value}
                                                        onChange={(e) => updateExpectResult(index, "value", e.target.value)}
                                                        placeholder="100%"
                                                        className="w-full h-[40px] px-3 border border-[#e3e4e8] rounded-lg outline-none font-bold"
                                                    />
                                                </div>
                                                <div className="space-y-1">
                                                    <label className="text-[12px] font-bold text-[#727586]">
                                                        Title
                                                    </label>
                                                    <input
                                                        type="text"
                                                        value={result.title}
                                                        onChange={(e) => updateExpectResult(index, "title", e.target.value)}
                                                        placeholder="Organic Traffic"
                                                        className="w-full h-[40px] px-3 border border-[#e3e4e8] rounded-lg outline-none font-bold"
                                                    />
                                                </div>
                                                <div className="space-y-1 md:col-span-2">
                                                    <label className="text-[12px] font-bold text-[#727586]">
                                                        Subtitle/Detail description
                                                    </label>
                                                    <input
                                                        type="text"
                                                        value={result.subtitle}
                                                        onChange={(e) => updateExpectResult(index, "subtitle", e.target.value)}
                                                        placeholder="Traffic boost on search engines..."
                                                        className="w-full h-[40px] px-3 border border-[#e3e4e8] rounded-lg outline-none"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}

                        {/* Brand Logos Section */}
                        {data.has_brands && (
                            <div className="bg-white rounded-[12px] border border-[#e3e4e8] shadow-sm p-5 md:p-6">
                                <div className="flex items-center justify-between mb-6">
                                    <div className="flex items-center gap-3">
                                        <BarChart3
                                            size={22}
                                            className="text-[#673ab7]"
                                        />
                                        <h2 className="text-[18px] font-bold text-[#2f3344]">
                                            Brand Logos
                                        </h2>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={addBrand}
                                        className="text-[#673ab7] text-[13px] font-bold hover:underline flex items-center gap-1"
                                    >
                                        <Plus size={16} /> Add Brand
                                    </button>
                                </div>

                                <div className="space-y-4">
                                    {(data.brands || []).map((brand, index) => (
                                        <div
                                            key={index}
                                            className="p-4 bg-[#f8f9fc] rounded-lg border border-[#e3e4e8] space-y-3"
                                        >
                                            <div className="flex justify-between items-start">
                                                <h3 className="text-[14px] font-bold text-[#673ab7]">
                                                    Brand {index + 1}
                                                </h3>
                                                {(data.brands || []).length > 1 && (
                                                    <button
                                                        type="button"
                                                        onClick={() => removeBrand(index)}
                                                        className="text-red-500 hover:text-red-700"
                                                    >
                                                        <Trash2 size={16} />
                                                    </button>
                                                )}
                                            </div>
                                            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                                <div className="md:col-span-2 space-y-1">
                                                    <label className="text-[12px] font-bold text-[#727586]">
                                                        Brand Name
                                                    </label>
                                                    <input
                                                        type="text"
                                                        value={brand.name}
                                                        onChange={(e) => updateBrand(index, "name", e.target.value)}
                                                        placeholder="e.g. Notion"
                                                        className="w-full h-[40px] px-3 border border-[#e3e4e8] rounded-lg outline-none font-bold"
                                                    />
                                                </div>
                                                <div className="space-y-1">
                                                    <label className="text-[12px] font-bold text-[#727586]">
                                                        Brand Logo
                                                    </label>
                                                    <div className="relative group">
                                                        <input
                                                            type="file"
                                                            onChange={(e) => updateBrand(index, "logo", e.target.files[0])}
                                                            accept="image/*"
                                                            className="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                                        />
                                                        <div className="flex flex-col items-center justify-center w-full h-[85px] bg-white border border-dashed border-[#e3e4e8] rounded-lg group-hover:border-[#673ab7] transition-all overflow-hidden px-2 py-1">
                                                            {brand.logo ? (
                                                                <div className="flex flex-col items-center gap-1">
                                                                    <img
                                                                        src={
                                                                            brand.logo instanceof File
                                                                                ? URL.createObjectURL(brand.logo)
                                                                                : (brand.logo.startsWith("http") || brand.logo.startsWith("data:") ? brand.logo : `/${brand.logo}`)
                                                                        }
                                                                        alt="Logo"
                                                                        className="h-10 w-full object-contain"
                                                                    />
                                                                    <span className="text-[10px] text-[#727586] truncate max-w-[120px]">
                                                                        {brand.logo instanceof File ? brand.logo.name : "Current Logo"}
                                                                    </span>
                                                                </div>
                                                            ) : (
                                                                <div className="flex flex-col items-center gap-1">
                                                                    <Upload size={16} className="text-[#a0a3af]" />
                                                                    <span className="text-[11px] text-[#727586] text-center">
                                                                        Upload Logo
                                                                    </span>
                                                                </div>
                                                            )}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>

                    {/* Right Column: Meta & Actions */}
                    <div className="col-span-12 lg:col-span-4 space-y-4 lg:sticky lg:top-5 text-[14px]">
                        {/* Status & Campaign Settings Card / Service Settings */}
                        <div className="bg-white rounded-[10px] border border-[#e3e4e8] shadow-sm p-5 space-y-4">
                            <div className="flex items-center gap-2.5 mb-1">
                                <CheckCircle2 size={18} className="text-[#673ab7]" />
                                <h2 className="text-[15px] font-bold text-[#2f3344]">
                                    Service Settings
                                </h2>
                            </div>

                            {/* Service Logo/Thumbnail */}
                            <div className="space-y-2">
                                <label className="block text-[13px] font-bold text-[#2f3344]">
                                    Thumbnail
                                </label>
                                <div className="relative group">
                                    <input
                                        type="file"
                                        onChange={(e) =>
                                            handleFileChange(e, "thumbnail")
                                        }
                                        accept="image/*"
                                        className="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                    />
                                    <div className="flex flex-col items-center justify-center w-full h-[140px] bg-[#f8f9fc] border-2 border-dashed border-[#e3e4e8] rounded-lg group-hover:border-[#673ab7] transition-all overflow-hidden">
                                        {thumbPreview || service.thumbnail ? (
                                            <img
                                                src={
                                                    thumbPreview ||
                                                    `/${service.thumbnail}`
                                                }
                                                alt="Thumb Preview"
                                                className="w-full h-full object-cover"
                                            />
                                        ) : (
                                            <div className="text-center p-2">
                                                <Upload
                                                    size={20}
                                                    className="text-[#a0a3af] mx-auto mb-1.5"
                                                />
                                                <p className="text-[11px] text-[#727586]">
                                                    Click to upload
                                                </p>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>

                            <div className="space-y-3.5 pt-4 border-t border-[#f1f2f4]">
                                {/* Toggle Status */}
                                <div className="flex items-center justify-between p-3 bg-[#f8f9fc] rounded-lg border border-[#e3e4e8]">
                                    <div className="space-y-0.5">
                                        <span className="text-[13px] font-bold text-[#2f3344] block">
                                            Service Status
                                        </span>
                                        <span className={`text-[11px] font-semibold ${data.status ? "text-green-600" : "text-slate-400"}`}>
                                            {data.status ? "Active (Visible)" : "Draft (Hidden)"}
                                        </span>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={() => setData("status", !data.status)}
                                        className={`relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none ${data.status ? "bg-[#673ab7]" : "bg-gray-200"}`}
                                    >
                                        <span className={`inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform ${data.status ? "translate-x-[20px]" : "translate-x-[2px]"}`} />
                                    </button>
                                </div>

                                {/* Toggle Campaign */}
                                <div className="flex items-center justify-between p-3 bg-[#f8f9fc] rounded-lg border border-[#e3e4e8]">
                                    <div className="space-y-0.5">
                                        <span className="text-[13px] font-bold text-[#2f3344] block">
                                            Is Campaign?
                                        </span>
                                        <span className={`text-[11px] font-semibold ${data.is_campaign ? "text-orange-600" : "text-slate-400"}`}>
                                            {data.is_campaign ? "Campaign (Has Tiers)" : "Standard Service"}
                                        </span>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={() => setData("is_campaign", !data.is_campaign)}
                                        className={`relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none ${data.is_campaign ? "bg-orange-500" : "bg-gray-200"}`}
                                    >
                                        <span className={`inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform ${data.is_campaign ? "translate-x-[20px]" : "translate-x-[2px]"}`} />
                                    </button>
                                </div>

                                {/* Toggle FAQ */}
                                <div className="flex items-center justify-between p-3 bg-[#f8f9fc] rounded-lg border border-[#e3e4e8]">
                                    <div className="space-y-0.5">
                                        <span className="text-[13px] font-bold text-[#2f3344] block">
                                            Enable FAQ?
                                        </span>
                                        <span className={`text-[11px] font-semibold ${data.has_faq ? "text-[#673ab7]" : "text-slate-400"}`}>
                                            {data.has_faq ? "FAQ Enabled" : "FAQ Disabled"}
                                        </span>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={() => setData("has_faq", !data.has_faq)}
                                        className={`relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none ${data.has_faq ? "bg-[#673ab7]" : "bg-gray-200"}`}
                                    >
                                        <span className={`inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform ${data.has_faq ? "translate-x-[20px]" : "translate-x-[2px]"}`} />
                                    </button>
                                </div>

                                {/* Toggle Secondary Features */}
                                <div className="flex items-center justify-between p-3 bg-[#f8f9fc] rounded-lg border border-[#e3e4e8]">
                                    <div className="space-y-0.5">
                                        <span className="text-[13px] font-bold text-[#2f3344] block">
                                            Secondary Features?
                                        </span>
                                        <span className={`text-[11px] font-semibold ${data.has_secondary_features ? "text-[#673ab7]" : "text-slate-400"}`}>
                                            {data.has_secondary_features ? "Enabled" : "Disabled"}
                                        </span>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={() => setData("has_secondary_features", !data.has_secondary_features)}
                                        className={`relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none ${data.has_secondary_features ? "bg-[#673ab7]" : "bg-gray-200"}`}
                                    >
                                        <span className={`inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform ${data.has_secondary_features ? "translate-x-[20px]" : "translate-x-[2px]"}`} />
                                    </button>
                                </div>

                                {/* Toggle Benefits Section */}
                                <div className="flex items-center justify-between p-3 bg-[#f8f9fc] rounded-lg border border-[#e3e4e8]">
                                    <div className="space-y-0.5">
                                        <span className="text-[13px] font-bold text-[#2f3344] block">
                                            Benefits Section?
                                        </span>
                                        <span className={`text-[11px] font-semibold ${data.has_benifite ? "text-[#673ab7]" : "text-slate-400"}`}>
                                            {data.has_benifite ? "Enabled" : "Disabled"}
                                        </span>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={() => setData("has_benifite", !data.has_benifite)}
                                        className={`relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none ${data.has_benifite ? "bg-[#673ab7]" : "bg-gray-200"}`}
                                    >
                                        <span className={`inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform ${data.has_benifite ? "translate-x-[20px]" : "translate-x-[2px]"}`} />
                                    </button>
                                </div>

                                {/* Toggle Why Us Section */}
                                <div className="flex items-center justify-between p-3 bg-[#f8f9fc] rounded-lg border border-[#e3e4e8]">
                                    <div className="space-y-0.5">
                                        <span className="text-[13px] font-bold text-[#2f3344] block">
                                            Why Us Section?
                                        </span>
                                        <span className={`text-[11px] font-semibold ${data.has_why_chose_us ? "text-[#673ab7]" : "text-slate-400"}`}>
                                            {data.has_why_chose_us ? "Enabled" : "Disabled"}
                                        </span>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={() => setData("has_why_chose_us", !data.has_why_chose_us)}
                                        className={`relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none ${data.has_why_chose_us ? "bg-[#673ab7]" : "bg-gray-200"}`}
                                    >
                                        <span className={`inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform ${data.has_why_chose_us ? "translate-x-[20px]" : "translate-x-[2px]"}`} />
                                    </button>
                                </div>

                                {/* Toggle Brands Section */}
                                <div className="flex items-center justify-between p-3 bg-[#f8f9fc] rounded-lg border border-[#e3e4e8]">
                                    <div className="space-y-0.5">
                                        <span className="text-[13px] font-bold text-[#2f3344] block">
                                            Brands Section?
                                        </span>
                                        <span className={`text-[11px] font-semibold ${data.has_brands ? "text-[#673ab7]" : "text-slate-400"}`}>
                                            {data.has_brands ? "Enabled" : "Disabled"}
                                        </span>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={() => setData("has_brands", !data.has_brands)}
                                        className={`relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none ${data.has_brands ? "bg-[#673ab7]" : "bg-gray-200"}`}
                                    >
                                        <span className={`inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform ${data.has_brands ? "translate-x-[20px]" : "translate-x-[2px]"}`} />
                                    </button>
                                </div>

                                {/* Toggle Expected Results Section */}
                                <div className="flex items-center justify-between p-3 bg-[#f8f9fc] rounded-lg border border-[#e3e4e8]">
                                    <div className="space-y-0.5">
                                        <span className="text-[13px] font-bold text-[#2f3344] block">
                                            Expected Results?
                                        </span>
                                        <span className={`text-[11px] font-semibold ${data.has_expect_result ? "text-[#673ab7]" : "text-slate-400"}`}>
                                            {data.has_expect_result ? "Enabled" : "Disabled"}
                                        </span>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={() => setData("has_expect_result", !data.has_expect_result)}
                                        className={`relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none ${data.has_expect_result ? "bg-[#673ab7]" : "bg-gray-200"}`}
                                    >
                                        <span className={`inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform ${data.has_expect_result ? "translate-x-[20px]" : "translate-x-[2px]"}`} />
                                    </button>
                                </div>
                            </div>

                            {/* Actions */}
                            <div className="pt-2 space-y-2">
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="w-full bg-[#673ab7] text-white py-2.5 rounded-lg text-[13px] font-bold hover:bg-[#5e35b1] transition-all flex items-center justify-center gap-2 disabled:opacity-50 shadow-md shadow-[#673ab7]/10"
                                >
                                    <CheckCircle2 size={16} />
                                    {processing ? "Updating..." : "Update Service"}
                                </button>
                                <Link
                                    href={route("admin.services.index")}
                                    className="w-full bg-slate-50 text-slate-500 py-2.5 rounded-lg text-[13px] font-bold hover:bg-slate-100 transition-all flex items-center justify-center border border-slate-200"
                                >
                                    Cancel
                                </Link>
                            </div>
                        </div>

                        {/* Video Section */}
                        <div className="bg-white rounded-[10px] border border-[#e3e4e8] shadow-sm p-5">
                            <div className="flex items-center gap-2.5 mb-3">
                                <div className="w-8 h-8 rounded-full bg-[#f4f0ff] flex items-center justify-center">
                                    <Upload
                                        size={14}
                                        className="text-[#673ab7]"
                                    />
                                </div>
                                <h2 className="text-[15px] font-bold text-[#2f3344]">
                                    Service Video
                                </h2>
                            </div>
                            <div className="space-y-4">
                                <div className="flex items-center gap-2 p-1 bg-[#f8f9fc] border border-[#e3e4e8] rounded-lg w-full">
                                    <button
                                        type="button"
                                        onClick={() => {
                                            setData((prev) => ({
                                                ...prev,
                                                video_source: "url",
                                                video_file: null,
                                            }));
                                        }}
                                        className={`flex-1 py-1.5 rounded-md text-[11px] font-bold transition-all ${data.video_source === "url" ? "bg-white text-[#673ab7] shadow-sm" : "text-[#727586] hover:text-[#2f3344]"}`}
                                    >
                                        URL
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => {
                                            setData((prev) => ({
                                                ...prev,
                                                video_source: "upload",
                                                video_url: "",
                                            }));
                                        }}
                                        className={`flex-1 py-1.5 rounded-md text-[11px] font-bold transition-all ${data.video_source === "upload" ? "bg-white text-[#673ab7] shadow-sm" : "text-[#727586] hover:text-[#2f3344]"}`}
                                    >
                                        Upload
                                    </button>
                                </div>

                                {data.video_source === "url" ? (
                                    <div className="space-y-1.5 animate-in fade-in slide-in-from-top-1 duration-200">
                                        <label className="block text-[11px] font-bold text-[#727586] uppercase tracking-wider">
                                            Video URL
                                        </label>
                                        <input
                                            type="text"
                                            value={data.video_url}
                                            onChange={(e) => {
                                                setData((prev) => ({
                                                    ...prev,
                                                    video_url: e.target.value,
                                                    video_file: null,
                                                }));
                                            }}
                                            placeholder="https://..."
                                            className="w-full h-[38px] px-3 border border-[#e3e4e8] rounded-lg focus:outline-none focus:border-[#673ab7] focus:ring-1 focus:ring-[#673ab7] transition-all text-[12px]"
                                        />
                                    </div>
                                ) : (
                                    <div className="space-y-1.5 animate-in fade-in slide-in-from-top-1 duration-200">
                                        <label className="block text-[11px] font-bold text-[#727586] uppercase tracking-wider">
                                            Upload File
                                        </label>
                                        <div className="relative">
                                            <input
                                                type="file"
                                                onChange={(e) =>
                                                    handleFileChange(
                                                        e,
                                                        "video_file",
                                                    )
                                                }
                                                accept="video/*"
                                                className="hidden"
                                                id="video_file"
                                            />
                                            <label
                                                htmlFor="video_file"
                                                className="flex items-center justify-between w-full h-[38px] px-3 border border-[#e3e4e8] border-dashed rounded-lg cursor-pointer hover:border-[#673ab7] hover:bg-[#fcfaff] transition-all group"
                                            >
                                                <span className="text-[12px] text-[#a0a3af] group-hover:text-[#673ab7] truncate pr-2">
                                                    {data.video_file
                                                        ? data.video_file.name
                                                        : "Select video..."}
                                                </span>
                                                <Upload
                                                    size={14}
                                                    className="text-[#a0a3af] group-hover:text-[#673ab7] flex-shrink-0"
                                                />
                                            </label>
                                        </div>
                                    </div>
                                )}

                                {(errors.video_url || errors.video_file) && (
                                    <p className="text-red-500 text-[11px]">
                                        {errors.video_url || errors.video_file}
                                    </p>
                                )}

                                {data.video_file &&
                                    data.video_source === "upload" && (
                                        <div className="p-2.5 bg-green-50 rounded-lg flex items-center justify-between border border-green-100">
                                            <span className="text-[11px] font-medium text-green-700 truncate mr-2">
                                                Ready: {data.video_file.name}
                                            </span>
                                            <button
                                                type="button"
                                                onClick={() =>
                                                    setData("video_file", null)
                                                }
                                                className="text-red-500 hover:text-red-700"
                                            >
                                                <Trash2 size={14} />
                                            </button>
                                        </div>
                                    )}
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </AdminLayout>
    );
}
