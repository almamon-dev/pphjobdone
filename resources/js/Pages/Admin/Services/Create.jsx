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
} from "lucide-react";

export default function Create() {
    const { data, setData, post, processing, errors } = useForm({
        title: "",
        subtitle: "",
        video_source: "url", // Added this
        video_url: "",
        video_file: null,
        thumbnail: null,
        pricing_plans: [{ name: "", price: "", features: [""] }],
        faqs: [{ question: "", answer: "" }],
        benefits: [{ title: "", points: [""], icon: null }],
        process_steps: [{ title: "", subtitle: "", icon: null }],
        timeline: [{ title: "", duration: "", price: "", items: [""] }],
        expect_results: [{ title: "", value: "", subtitle: "" }],
        section_one: {
            title: "",
            subtitle: "",
            description: "",
            points: [""],
            button_text: "",
            button_url: "",
        },
        section_two: {
            title: "",
            subtitle: "",
            description: "",
            points: [""],
            button_text: "",
            button_url: "",
        },
        section_one_image: null,
        section_two_image: null,
        status: true,
    });

    const [thumbPreview, setThumbPreview] = useState(null);
    const [sectionOnePreview, setSectionOnePreview] = useState(null);
    const [sectionTwoPreview, setSectionTwoPreview] = useState(null);

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

    // Helper functions for new sections
    const addProcessStep = () => {
        setData("process_steps", [
            ...data.process_steps,
            { title: "", subtitle: "", icon: "" },
        ]);
    };

    const removeProcessStep = (index) => {
        const newSteps = data.process_steps.filter((_, i) => i !== index);
        setData("process_steps", newSteps);
    };

    const updateProcessStep = (index, field, value) => {
        const newSteps = [...data.process_steps];
        newSteps[index][field] = value;
        setData("process_steps", newSteps);
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
        setData("benefits", [
            ...data.benefits,
            { title: "", points: [""], icon: null },
        ]);
    };

    const removeBenefit = (index) => {
        const newBenefits = data.benefits.filter((_, i) => i !== index);
        setData("benefits", newBenefits);
    };

    const updateBenefit = (index, field, value) => {
        const newBenefits = [...data.benefits];
        newBenefits[index][field] = value;
        setData("benefits", newBenefits);
    };

    const addBenefitPoint = (benefitIndex) => {
        const newBenefits = [...data.benefits];
        newBenefits[benefitIndex].points = [
            ...newBenefits[benefitIndex].points,
            "",
        ];
        setData("benefits", newBenefits);
    };

    const removeBenefitPoint = (benefitIndex, pointIndex) => {
        const newBenefits = [...data.benefits];
        newBenefits[benefitIndex].points = newBenefits[
            benefitIndex
        ].points.filter((_, i) => i !== pointIndex);
        setData("benefits", newBenefits);
    };

    const updateBenefitPoint = (benefitIndex, pointIndex, value) => {
        const newBenefits = [...data.benefits];
        newBenefits[benefitIndex].points[pointIndex] = value;
        setData("benefits", newBenefits);
    };

    const addPlan = () => {
        setData("pricing_plans", [
            ...data.pricing_plans,
            { name: "", price: "", features: [""] },
        ]);
    };

    const removePlan = (index) => {
        const newPlans = data.pricing_plans.filter((_, i) => i !== index);
        setData("pricing_plans", newPlans);
    };

    const updatePlan = (index, field, value) => {
        const newPlans = [...data.pricing_plans];
        newPlans[index][field] = value;
        setData("pricing_plans", newPlans);
    };

    const addPlanFeature = (planIndex) => {
        const newPlans = [...data.pricing_plans];
        newPlans[planIndex].features.push("");
        setData("pricing_plans", newPlans);
    };

    const updatePlanFeature = (planIndex, featureIndex, value) => {
        const newPlans = [...data.pricing_plans];
        newPlans[planIndex].features[featureIndex] = value;
        setData("pricing_plans", newPlans);
    };

    const removePlanFeature = (planIndex, featureIndex) => {
        const newPlans = [...data.pricing_plans];
        newPlans[planIndex].features = newPlans[planIndex].features.filter(
            (_, i) => i !== featureIndex,
        );
        setData("pricing_plans", newPlans);
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

    // Timeline Helpers
    const addTimelinePhase = () => {
        setData("timeline", [
            ...data.timeline,
            { title: "", duration: "", price: "", items: [""] },
        ]);
    };

    const removeTimelinePhase = (index) => {
        const newTimeline = data.timeline.filter((_, i) => i !== index);
        setData("timeline", newTimeline);
    };

    const updateTimelinePhase = (index, field, value) => {
        const newTimeline = [...data.timeline];
        newTimeline[index][field] = value;
        setData("timeline", newTimeline);
    };

    const addTimelineItem = (phaseIndex) => {
        const newTimeline = [...data.timeline];
        newTimeline[phaseIndex].items = [...newTimeline[phaseIndex].items, ""];
        setData("timeline", newTimeline);
    };

    const updateTimelineItem = (phaseIndex, itemIndex, value) => {
        const newTimeline = [...data.timeline];
        newTimeline[phaseIndex].items[itemIndex] = value;
        setData("timeline", newTimeline);
    };

    const removeTimelineItem = (phaseIndex, itemIndex) => {
        const newTimeline = [...data.timeline];
        newTimeline[phaseIndex].items = newTimeline[phaseIndex].items.filter(
            (_, i) => i !== itemIndex,
        );
        setData("timeline", newTimeline);
    };

    // Expected Results Helpers
    const addResult = () => {
        setData("expect_results", [
            ...data.expect_results,
            { title: "", value: "", subtitle: "" },
        ]);
    };

    const removeResult = (index) => {
        const newResults = data.expect_results.filter((_, i) => i !== index);
        setData("expect_results", newResults);
    };

    const updateResult = (index, field, value) => {
        const newResults = [...data.expect_results];
        newResults[index][field] = value;
        setData("expect_results", newResults);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route("admin.services.store"));
    };

    return (
        <AdminLayout>
            <Head title="Add Service" />

            <div className="space-y-4 max-w-[1200px] mx-auto pb-20">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <h1 className="text-[24px] font-bold text-[#2f3344] tracking-tight">
                            Add New Service
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
                            <span>Create</span>
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

                        {/* Pricing Section */}
                        <div className="bg-white rounded-[10px] border border-[#e3e4e8] shadow-sm p-5">
                            <div className="flex items-center justify-between mb-4">
                                <div className="flex items-center gap-2.5">
                                    <Plus
                                        size={20}
                                        className="text-[#673ab7]"
                                    />
                                    <h2 className="text-[16px] font-bold text-[#2f3344]">
                                        Pricing Plans
                                    </h2>
                                </div>
                                <button
                                    type="button"
                                    onClick={addPlan}
                                    className="text-[#673ab7] text-[13px] font-bold hover:underline flex items-center gap-1"
                                >
                                    <Plus size={16} /> Add Plan
                                </button>
                            </div>

                            <div className="space-y-6">
                                {data.pricing_plans.map((plan, pIndex) => (
                                    <div
                                        key={pIndex}
                                        className="p-6 bg-[#f8f9fc] rounded-xl border border-[#e3e4e8] space-y-4"
                                    >
                                        <div className="flex justify-between items-start">
                                            <h3 className="text-[14px] font-bold text-[#673ab7]">
                                                Plan {pIndex + 1}
                                            </h3>
                                            {data.pricing_plans.length > 1 && (
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        removePlan(pIndex)
                                                    }
                                                    className="text-red-500 hover:text-red-700"
                                                >
                                                    <Trash2 size={16} />
                                                </button>
                                            )}
                                        </div>
                                        <div className="grid grid-cols-2 gap-4">
                                            <div className="space-y-1">
                                                <label className="text-[12px] font-bold text-[#727586]">
                                                    Plan Name
                                                </label>
                                                <input
                                                    type="text"
                                                    value={plan.name}
                                                    onChange={(e) =>
                                                        updatePlan(
                                                            pIndex,
                                                            "name",
                                                            e.target.value,
                                                        )
                                                    }
                                                    className="w-full h-[40px] px-3 border border-[#e3e4e8] rounded-lg outline-none"
                                                />
                                            </div>
                                            <div className="space-y-1">
                                                <label className="text-[12px] font-bold text-[#727586]">
                                                    Price
                                                </label>
                                                <input
                                                    type="text"
                                                    value={plan.price}
                                                    onChange={(e) =>
                                                        updatePlan(
                                                            pIndex,
                                                            "price",
                                                            e.target.value,
                                                        )
                                                    }
                                                    placeholder="$"
                                                    className="w-full h-[40px] px-3 border border-[#e3e4e8] rounded-lg outline-none"
                                                />
                                            </div>
                                        </div>
                                        <div className="space-y-2">
                                            <div className="flex justify-between items-center">
                                                <label className="text-[12px] font-bold text-[#727586]">
                                                    Plan Features
                                                </label>
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        addPlanFeature(pIndex)
                                                    }
                                                    className="text-[#673ab7] text-[11px] font-bold"
                                                >
                                                    + Feature
                                                </button>
                                            </div>
                                            {plan.features.map(
                                                (pf, pfIndex) => (
                                                    <div
                                                        key={pfIndex}
                                                        className="flex gap-2"
                                                    >
                                                        <input
                                                            type="text"
                                                            value={pf}
                                                            onChange={(e) =>
                                                                updatePlanFeature(
                                                                    pIndex,
                                                                    pfIndex,
                                                                    e.target
                                                                        .value,
                                                                )
                                                            }
                                                            className="flex-1 h-[36px] px-3 bg-white border border-[#e3e4e8] rounded-lg text-[13px] outline-none"
                                                        />
                                                        {plan.features.length >
                                                            1 && (
                                                            <button
                                                                type="button"
                                                                onClick={() =>
                                                                    removePlanFeature(
                                                                        pIndex,
                                                                        pfIndex,
                                                                    )
                                                                }
                                                                className="text-red-400"
                                                            >
                                                                <MinusCircle
                                                                    size={16}
                                                                />
                                                            </button>
                                                        )}
                                                    </div>
                                                ),
                                            )}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* Benefits Section */}
                        <div className="bg-white rounded-[12px] border border-[#e3e4e8] shadow-sm p-8">
                            <div className="flex items-center justify-between mb-6">
                                <div className="flex items-center gap-3">
                                    <CheckCircle2
                                        size={22}
                                        className="text-[#673ab7]"
                                    />
                                    <h2 className="text-[18px] font-bold text-[#2f3344]">
                                        What's Included (Inclusions)
                                    </h2>
                                </div>
                                <button
                                    type="button"
                                    onClick={addBenefit}
                                    className="text-[#673ab7] text-[13px] font-bold hover:underline flex items-center gap-1"
                                >
                                    <Plus size={16} /> Add Benefit
                                </button>
                            </div>

                            <div className="space-y-4">
                                {data.benefits.map((benefit, index) => (
                                    <div
                                        key={index}
                                        className="p-6 bg-[#f8f9fc] rounded-xl border border-[#e3e4e8] space-y-4"
                                    >
                                        <div className="flex justify-between items-start">
                                            <h3 className="text-[14px] font-bold text-[#673ab7]">
                                                Benefit {index + 1}
                                            </h3>
                                            {data.benefits.length > 1 && (
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        removeBenefit(index)
                                                    }
                                                    className="text-red-500 hover:text-red-700"
                                                >
                                                    <Trash2 size={16} />
                                                </button>
                                            )}
                                        </div>
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div className="space-y-4">
                                                <div className="space-y-1">
                                                    <label className="text-[12px] font-bold text-[#727586]">
                                                        Inclusion Title
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
                                                        placeholder="e.g., On-Page Optimization"
                                                        className="w-full h-[40px] px-3 border border-[#e3e4e8] rounded-lg outline-none font-bold"
                                                    />
                                                </div>
                                                <div className="space-y-2">
                                                    <div className="flex justify-between items-center">
                                                        <label className="text-[12px] font-bold text-[#727586]">
                                                            Inclusion Points
                                                        </label>
                                                        <button
                                                            type="button"
                                                            onClick={() =>
                                                                addBenefitPoint(
                                                                    index,
                                                                )
                                                            }
                                                            className="text-[#673ab7] text-[11px] font-bold"
                                                        >
                                                            + Add Point
                                                        </button>
                                                    </div>
                                                    <div className="space-y-2">
                                                        {benefit.points.map(
                                                            (point, pIdx) => (
                                                                <div
                                                                    key={pIdx}
                                                                    className="flex gap-2"
                                                                >
                                                                    <input
                                                                        type="text"
                                                                        value={
                                                                            point
                                                                        }
                                                                        onChange={(
                                                                            e,
                                                                        ) =>
                                                                            updateBenefitPoint(
                                                                                index,
                                                                                pIdx,
                                                                                e
                                                                                    .target
                                                                                    .value,
                                                                            )
                                                                        }
                                                                        placeholder="e.g., Title tags & meta descriptions"
                                                                        className="flex-1 h-[36px] px-3 bg-white border border-[#e3e4e8] rounded-lg text-[13px] outline-none"
                                                                    />
                                                                    {benefit
                                                                        .points
                                                                        .length >
                                                                        1 && (
                                                                        <button
                                                                            type="button"
                                                                            onClick={() =>
                                                                                removeBenefitPoint(
                                                                                    index,
                                                                                    pIdx,
                                                                                )
                                                                            }
                                                                            className="text-red-400"
                                                                        >
                                                                            <MinusCircle
                                                                                size={
                                                                                    14
                                                                                }
                                                                            />
                                                                        </button>
                                                                    )}
                                                                </div>
                                                            ),
                                                        )}
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="space-y-1">
                                                <label className="text-[12px] font-bold text-[#727586]">
                                                    Benefit Icon
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
                                                    <div className="flex flex-col items-center justify-center w-full h-[135px] bg-white border-2 border-dashed border-[#e3e4e8] rounded-xl group-hover:border-[#673ab7] transition-all overflow-hidden px-3">
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
                                                                    alt="Benefit Icon"
                                                                    className="h-12 w-12 object-contain"
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
                                                                <span className="text-[12px] text-[#727586]">
                                                                    Upload
                                                                    Benefit Icon
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

                        {/* FAQ Section */}
                        <div className="bg-white rounded-[12px] border border-[#e3e4e8] shadow-sm p-8">
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

                        {/* Proposed Timeline & Investment Section */}
                        <div className="bg-white rounded-[12px] border border-[#e3e4e8] shadow-sm p-8">
                            <div className="flex items-center justify-between mb-6">
                                <div className="flex items-center gap-3">
                                    <Clock
                                        size={22}
                                        className="text-[#673ab7]"
                                    />
                                    <h2 className="text-[18px] font-bold text-[#2f3344]">
                                        Proposed Timeline & Investment
                                    </h2>
                                </div>
                                <button
                                    type="button"
                                    onClick={addTimelinePhase}
                                    className="text-[#673ab7] text-[13px] font-bold hover:underline flex items-center gap-1"
                                >
                                    <Plus size={16} /> Add Phase
                                </button>
                            </div>

                            <div className="space-y-6">
                                {data.timeline.map((phase, pIdx) => (
                                    <div
                                        key={pIdx}
                                        className="p-6 bg-[#f8f9fc] rounded-xl border border-[#e3e4e8] space-y-4"
                                    >
                                        <div className="flex justify-between items-center bg-white p-3 rounded-lg border border-[#e3e4e8]">
                                            <div className="flex items-center gap-3">
                                                <div className="w-8 h-8 rounded-full bg-[#673ab7] text-white flex items-center justify-center font-bold text-[14px]">
                                                    {String(pIdx + 1).padStart(
                                                        2,
                                                        "0",
                                                    )}
                                                </div>
                                                <input
                                                    type="text"
                                                    value={phase.title}
                                                    onChange={(e) =>
                                                        updateTimelinePhase(
                                                            pIdx,
                                                            "title",
                                                            e.target.value,
                                                        )
                                                    }
                                                    placeholder="Phase Title (e.g., Technical Foundation)"
                                                    className="border-none focus:ring-0 font-bold text-[#2f3344] p-0 text-[15px] bg-transparent w-[300px]"
                                                />
                                            </div>
                                            {data.timeline.length > 1 && (
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        removeTimelinePhase(
                                                            pIdx,
                                                        )
                                                    }
                                                    className="text-red-500 hover:text-red-700 p-1"
                                                >
                                                    <Trash2 size={16} />
                                                </button>
                                            )}
                                        </div>

                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div className="space-y-1">
                                                <label className="text-[12px] font-bold text-[#727586] flex items-center gap-1">
                                                    <Clock size={12} />{" "}
                                                    Timeframe
                                                </label>
                                                <input
                                                    type="text"
                                                    value={phase.duration}
                                                    onChange={(e) =>
                                                        updateTimelinePhase(
                                                            pIdx,
                                                            "duration",
                                                            e.target.value,
                                                        )
                                                    }
                                                    placeholder="e.g., Week 1-2"
                                                    className="w-full h-[38px] px-3 bg-white border border-[#e3e4e8] rounded-lg outline-none text-[13px]"
                                                />
                                            </div>
                                            <div className="space-y-1">
                                                <label className="text-[12px] font-bold text-[#727586] flex items-center gap-1">
                                                    <DollarSign size={12} />{" "}
                                                    Investment
                                                </label>
                                                <input
                                                    type="text"
                                                    value={phase.price}
                                                    onChange={(e) =>
                                                        updateTimelinePhase(
                                                            pIdx,
                                                            "price",
                                                            e.target.value,
                                                        )
                                                    }
                                                    placeholder="e.g., $1,500"
                                                    className="w-full h-[38px] px-3 bg-white border border-[#e3e4e8] rounded-lg outline-none text-[13px]"
                                                />
                                            </div>
                                        </div>

                                        <div className="space-y-2">
                                            <div className="flex justify-between items-center">
                                                <label className="text-[12px] font-bold text-[#727586]">
                                                    Phase Items/Tasks
                                                </label>
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        addTimelineItem(pIdx)
                                                    }
                                                    className="text-[#673ab7] text-[11px] font-bold"
                                                >
                                                    + Add Item
                                                </button>
                                            </div>
                                            <div className="space-y-2">
                                                {phase.items.map(
                                                    (item, iIdx) => (
                                                        <div
                                                            key={iIdx}
                                                            className="flex gap-2"
                                                        >
                                                            <input
                                                                type="text"
                                                                value={item}
                                                                onChange={(e) =>
                                                                    updateTimelineItem(
                                                                        pIdx,
                                                                        iIdx,
                                                                        e.target
                                                                            .value,
                                                                    )
                                                                }
                                                                placeholder="e.g., Technical SEO Audit & Fixes"
                                                                className="flex-1 h-[36px] px-3 bg-white border border-[#e3e4e8] rounded-lg text-[13px] outline-none"
                                                            />
                                                            {phase.items
                                                                .length > 1 && (
                                                                <button
                                                                    type="button"
                                                                    onClick={() =>
                                                                        removeTimelineItem(
                                                                            pIdx,
                                                                            iIdx,
                                                                        )
                                                                    }
                                                                    className="text-red-400 hover:text-red-500"
                                                                >
                                                                    <MinusCircle
                                                                        size={
                                                                            16
                                                                        }
                                                                    />
                                                                </button>
                                                            )}
                                                        </div>
                                                    ),
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* Expected Results Section */}
                        <div className="bg-white rounded-[12px] border border-[#e3e4e8] shadow-sm p-8">
                            <div className="flex items-center justify-between mb-6">
                                <div className="flex items-center gap-3">
                                    <BarChart3
                                        size={22}
                                        className="text-[#673ab7]"
                                    />
                                    <h2 className="text-[18px] font-bold text-[#2f3344]">
                                        Expected Results
                                    </h2>
                                </div>
                                <button
                                    type="button"
                                    onClick={addResult}
                                    className="text-[#673ab7] text-[13px] font-bold hover:underline flex items-center gap-1"
                                >
                                    <Plus size={16} /> Add Result
                                </button>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {data.expect_results.map((result, rIdx) => (
                                    <div
                                        key={rIdx}
                                        className="p-5 bg-[#f8f9fc] rounded-xl border border-[#e3e4e8] space-y-3 relative group"
                                    >
                                        <button
                                            type="button"
                                            onClick={() => removeResult(rIdx)}
                                            className="absolute top-2 right-2 text-red-400 opacity-0 group-hover:opacity-100 transition-opacity"
                                        >
                                            <Trash2 size={14} />
                                        </button>

                                        <div className="space-y-1">
                                            <label className="text-[11px] font-bold text-[#727586] uppercase tracking-wider">
                                                Result Title
                                            </label>
                                            <input
                                                type="text"
                                                value={result.title}
                                                onChange={(e) =>
                                                    updateResult(
                                                        rIdx,
                                                        "title",
                                                        e.target.value,
                                                    )
                                                }
                                                placeholder="e.g., Organic Traffic"
                                                className="w-full h-[38px] px-3 bg-white border border-[#e3e4e8] rounded-lg outline-none text-[13px]"
                                            />
                                        </div>

                                        <div className="grid grid-cols-2 gap-3">
                                            <div className="space-y-1">
                                                <label className="text-[11px] font-bold text-[#727586] uppercase tracking-wider">
                                                    Growth/Value
                                                </label>
                                                <div className="relative">
                                                    <TrendingUp
                                                        size={14}
                                                        className="absolute left-3 top-1/2 -translate-y-1/2 text-green-500"
                                                    />
                                                    <input
                                                        type="text"
                                                        value={result.value}
                                                        onChange={(e) =>
                                                            updateResult(
                                                                rIdx,
                                                                "value",
                                                                e.target.value,
                                                            )
                                                        }
                                                        placeholder="+150%"
                                                        className="w-full h-[38px] pl-9 pr-3 bg-white border border-[#e3e4e8] rounded-lg outline-none text-[13px] font-bold text-green-600"
                                                    />
                                                </div>
                                            </div>
                                            <div className="space-y-1">
                                                <label className="text-[11px] font-bold text-[#727586] uppercase tracking-wider">
                                                    Timeframe
                                                </label>
                                                <input
                                                    type="text"
                                                    value={result.subtitle}
                                                    onChange={(e) =>
                                                        updateResult(
                                                            rIdx,
                                                            "subtitle",
                                                            e.target.value,
                                                        )
                                                    }
                                                    placeholder="6 Months"
                                                    className="w-full h-[38px] px-3 bg-white border border-[#e3e4e8] rounded-lg outline-none text-[13px]"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* Process Steps Section */}
                        <div className="bg-white rounded-[12px] border border-[#e3e4e8] shadow-sm p-8">
                            <div className="flex items-center justify-between mb-6">
                                <div className="flex items-center gap-3">
                                    <Briefcase
                                        size={22}
                                        className="text-[#673ab7]"
                                    />
                                    <h2 className="text-[18px] font-bold text-[#2f3344]">
                                        Monthly Process Steps
                                    </h2>
                                </div>
                                <button
                                    type="button"
                                    onClick={addProcessStep}
                                    className="text-[#673ab7] text-[13px] font-bold hover:underline flex items-center gap-1"
                                >
                                    <Plus size={16} /> Add Step
                                </button>
                            </div>

                            <div className="space-y-4">
                                {data.process_steps.map((step, index) => (
                                    <div
                                        key={index}
                                        className="p-6 bg-[#f8f9fc] rounded-xl border border-[#e3e4e8] space-y-4"
                                    >
                                        <div className="flex justify-between items-start">
                                            <h3 className="text-[14px] font-bold text-[#673ab7]">
                                                Step {index + 1}
                                            </h3>
                                            {data.process_steps.length > 1 && (
                                                <button
                                                    type="button"
                                                    onClick={() =>
                                                        removeProcessStep(index)
                                                    }
                                                    className="text-red-500 hover:text-red-700"
                                                >
                                                    <Trash2 size={16} />
                                                </button>
                                            )}
                                        </div>
                                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div className="space-y-1">
                                                <label className="text-[12px] font-bold text-[#727586]">
                                                    Step Title
                                                </label>
                                                <input
                                                    type="text"
                                                    value={step.title}
                                                    onChange={(e) =>
                                                        updateProcessStep(
                                                            index,
                                                            "title",
                                                            e.target.value,
                                                        )
                                                    }
                                                    className="w-full h-[40px] px-3 border border-[#e3e4e8] rounded-lg outline-none"
                                                />
                                            </div>
                                            <div className="space-y-1">
                                                <label className="text-[12px] font-bold text-[#727586]">
                                                    Step Subtitle/Text
                                                </label>
                                                <input
                                                    type="text"
                                                    value={step.subtitle}
                                                    onChange={(e) =>
                                                        updateProcessStep(
                                                            index,
                                                            "subtitle",
                                                            e.target.value,
                                                        )
                                                    }
                                                    className="w-full h-[40px] px-3 border border-[#e3e4e8] rounded-lg outline-none"
                                                />
                                            </div>
                                            <div className="space-y-1">
                                                <label className="text-[12px] font-bold text-[#727586]">
                                                    Step Icon
                                                </label>
                                                <div className="relative group">
                                                    <input
                                                        type="file"
                                                        onChange={(e) =>
                                                            updateProcessStep(
                                                                index,
                                                                "icon",
                                                                e.target
                                                                    .files[0],
                                                            )
                                                        }
                                                        accept="image/*"
                                                        className="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                                    />
                                                    <div className="flex flex-col items-center justify-center w-full h-[40px] bg-white border border-[#e3e4e8] rounded-lg group-hover:border-[#673ab7] transition-all overflow-hidden px-3">
                                                        {step.icon ? (
                                                            <div className="flex items-center gap-2">
                                                                <img
                                                                    src={
                                                                        step.icon instanceof
                                                                        File
                                                                            ? URL.createObjectURL(
                                                                                  step.icon,
                                                                              )
                                                                            : `/${step.icon}`
                                                                    }
                                                                    alt="Step Icon"
                                                                    className="h-6 w-6 object-contain"
                                                                />
                                                                <span className="text-[11px] text-[#727586] truncate max-w-[100px]">
                                                                    {step.icon instanceof
                                                                    File
                                                                        ? step
                                                                              .icon
                                                                              .name
                                                                        : "Current Icon"}
                                                                </span>
                                                            </div>
                                                        ) : (
                                                            <div className="flex items-center gap-2">
                                                                <Upload
                                                                    size={14}
                                                                    className="text-[#a0a3af]"
                                                                />
                                                                <span className="text-[11px] text-[#727586]">
                                                                    Upload
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

                        {/* Content Section One */}
                        <div className="bg-white rounded-[10px] border border-[#e3e4e8] shadow-sm p-5">
                            <div className="flex items-center gap-2.5 mb-4">
                                <Briefcase
                                    size={20}
                                    className="text-[#673ab7]"
                                />
                                <h2 className="text-[16px] font-bold text-[#2f3344]">
                                    Primary Content Section (e.g., Features)
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
                                                {sectionOnePreview ? (
                                                    <img
                                                        src={sectionOnePreview}
                                                        alt="Section Preview"
                                                        className="w-full h-full object-contain"
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

                        {/* Content Section Two */}
                        <div className="bg-white rounded-[10px] border border-[#e3e4e8] shadow-sm p-5">
                            <div className="flex items-center gap-2.5 mb-4">
                                <Briefcase
                                    size={20}
                                    className="text-[#673ab7]"
                                />
                                <h2 className="text-[16px] font-bold text-[#2f3344]">
                                    Secondary Content Section (e.g., Why Us)
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
                                                {sectionTwoPreview ? (
                                                    <img
                                                        src={sectionTwoPreview}
                                                        alt="Section Preview"
                                                        className="w-full h-full object-contain"
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
                    </div>

                    {/* Right Column: Meta & Actions */}
                    <div className="col-span-12 lg:col-span-4 space-y-5 lg:sticky lg:top-5 text-[14px]">
                        {/* Service Logo/Thumbnail */}
                        <div className="bg-white rounded-[10px] border border-[#e3e4e8] shadow-sm p-5">
                            <div className="flex items-center gap-2.5 mb-3">
                                <Upload size={18} className="text-[#673ab7]" />
                                <h2 className="text-[15px] font-bold text-[#2f3344]">
                                    Thumbnail
                                </h2>
                            </div>
                            <div className="space-y-3">
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
                                        {thumbPreview ? (
                                            <img
                                                src={thumbPreview}
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

                        {/* Status Card */}
                        <div className="bg-white rounded-[10px] border border-[#e3e4e8] shadow-sm p-5">
                            <div className="flex items-center justify-between gap-3">
                                <div className="space-y-0.5">
                                    <span className="text-[13px] font-bold text-[#2f3344] block">
                                        Toggle Status
                                    </span>
                                    <span
                                        className={`text-[11px] font-medium ${data.status ? "text-green-500" : "text-slate-400"}`}
                                    >
                                        {data.status
                                            ? "Active (Visible)"
                                            : "Draft (Hidden)"}
                                    </span>
                                </div>
                                <button
                                    type="button"
                                    onClick={() =>
                                        setData("status", !data.status)
                                    }
                                    className={`relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none ${data.status ? "bg-[#673ab7]" : "bg-gray-200"}`}
                                >
                                    <span
                                        className={`inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform ${data.status ? "translate-x-[20px]" : "translate-x-[2px]"}`}
                                    />
                                </button>
                            </div>
                        </div>

                        {/* Action Card */}
                        <div className="bg-white rounded-[10px] border border-[#e3e4e8] shadow-lg p-5 space-y-2.5">
                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full bg-[#673ab7] text-white py-2.5 rounded-lg text-[13px] font-bold hover:bg-[#5e35b1] transition-all flex items-center justify-center gap-2 disabled:opacity-50"
                            >
                                <CheckCircle2 size={16} />
                                {processing ? "Saving..." : "Save Service"}
                            </button>
                            <Link
                                href={route("admin.services.index")}
                                className="w-full bg-slate-50 text-slate-500 py-2.5 rounded-lg text-[13px] font-bold hover:bg-slate-100 transition-all flex items-center justify-center"
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
