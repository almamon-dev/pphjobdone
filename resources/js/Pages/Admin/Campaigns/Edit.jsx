import React from "react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Head, Link, useForm } from "@inertiajs/react";
import {
    Home,
    Zap,
    Trash2,
    CheckCircle2,
    Briefcase,
    Plus,
    Layers,
    Type,
} from "lucide-react";
import SelectInput from "@/Components/SelectInput";

export default function Edit({ campaign, services }) {
    const { data, setData, post, processing, errors } = useForm({
        service_id: campaign.service_id,
        title: campaign.title,
        subtitle: campaign.subtitle,
        status: campaign.status,
        tiers: campaign.tiers.map(t => ({
            id: t.id,
            price: t.price,
            features: t.features.map(f => ({ 
                feature_text: f.feature_text,
                sub_items: Array.isArray(f.sub_items) ? f.sub_items : []
            }))
        })),
        _method: "PUT",
    });

    const addTier = () => {
        setData("tiers", [...data.tiers, { price: "", features: [{ feature_text: "", sub_items: [] }] }]);
    };

    const removeTier = (index) => {
        if (confirm("Are you sure you want to remove this price tier?")) {
            const newTiers = data.tiers.filter((_, i) => i !== index);
            setData("tiers", newTiers);
        }
    };

    const updateTierPrice = (index, price) => {
        const newTiers = [...data.tiers];
        newTiers[index].price = price;
        setData("tiers", newTiers);
    };

    const addFeature = (tierIndex) => {
        const newTiers = [...data.tiers];
        newTiers[tierIndex].features.push({ feature_text: "", sub_items: [] });
        setData("tiers", newTiers);
    };

    const removeFeature = (tierIndex, featureIndex) => {
        const newTiers = [...data.tiers];
        newTiers[tierIndex].features = newTiers[tierIndex].features.filter((_, i) => i !== featureIndex);
        setData("tiers", newTiers);
    };

    const updateFeatureText = (tierIndex, featureIndex, text) => {
        const newTiers = [...data.tiers];
        newTiers[tierIndex].features[featureIndex].feature_text = text;
        setData("tiers", newTiers);
    };

    const addSubItem = (tierIndex, featureIndex) => {
        const newTiers = [...data.tiers];
        newTiers[tierIndex].features[featureIndex].sub_items.push("");
        setData("tiers", newTiers);
    };

    const removeSubItem = (tierIndex, featureIndex, subItemIndex) => {
        const newTiers = [...data.tiers];
        newTiers[tierIndex].features[featureIndex].sub_items = newTiers[tierIndex].features[featureIndex].sub_items.filter((_, i) => i !== subItemIndex);
        setData("tiers", newTiers);
    };

    const updateSubItemText = (tierIndex, featureIndex, subItemIndex, text) => {
        const newTiers = [...data.tiers];
        newTiers[tierIndex].features[featureIndex].sub_items[subItemIndex] = text;
        setData("tiers", newTiers);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route("admin.campaigns.update", campaign.id));
    };

    const serviceOptions = services.map(s => ({ value: s.id, label: s.title }));

    return (
        <AdminLayout>
            <Head title="Edit Campaign" />

            <div className="max-w-full mx-auto pb-20">
                {/* Header */}
                <div className="mb-8">
                    <div className="flex items-center gap-2 text-[12px] text-[#727586] mb-2 uppercase font-bold tracking-wider">
                        <Home size={14} />
                        <span>/</span>
                        <Link href={route("admin.campaigns.index")}>Campaigns</Link>
                        <span>/</span>
                        <span className="text-[#673ab7]">Edit</span>
                    </div>
                    <h1 className="text-[24px] font-bold text-[#2f3344] tracking-tight">
                        Update Campaign Group
                    </h1>
                </div>

                <form onSubmit={handleSubmit} className="grid grid-cols-12 gap-8">
                    {/* Main Content */}
                    <div className="col-span-12 lg:col-span-8 space-y-4">
                        <div className="bg-white rounded-[10px] border border-[#e3e4e8] shadow-sm overflow-hidden">
                            <div className="px-4 py-3 border-b border-[#f1f2f4] bg-[#fafbfc] flex items-center gap-2 text-[#2f3344] font-bold text-[11px] uppercase tracking-wider">
                                <Zap size={14} className="text-[#673ab7]" />
                                <span>Basic Information</span>
                            </div>

                            <div className="p-4 space-y-4">
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div className="space-y-1">
                                        <label className="text-[12px] font-bold text-[#2f3344]">Campaign Title *</label>
                                        <input
                                            type="text"
                                            value={data.title}
                                            onChange={(e) => setData("title", e.target.value)}
                                            className={`w-full h-[38px] px-3 bg-white border ${errors.title ? "border-red-500" : "border-[#e3e4e8]"} rounded-[6px] focus:border-[#673ab7] outline-none transition-all text-[13px]`}
                                        />
                                        {errors.title && <p className="text-red-500 text-[11px] mt-1 font-bold">{errors.title}</p>}
                                    </div>

                                    <div className="space-y-1">
                                        <label className="text-[12px] font-bold text-[#2f3344]">Subtitle</label>
                                        <input
                                            type="text"
                                            value={data.subtitle}
                                            onChange={(e) => setData("subtitle", e.target.value)}
                                            className="w-full h-[38px] px-3 bg-white border border-[#e3e4e8] rounded-[6px] focus:border-[#673ab7] outline-none transition-all text-[13px]"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Tiers */}
                        <div className="space-y-3">
                            <div className="flex items-center justify-between px-2">
                                <h2 className="text-[14px] font-bold text-[#2f3344] flex items-center gap-2">
                                    <Layers size={16} className="text-[#673ab7]" />
                                    Price Tiers
                                </h2>
                                <button
                                    type="button"
                                    onClick={addTier}
                                    className="text-[11px] font-bold text-[#673ab7] hover:underline flex items-center gap-1"
                                >
                                    <Plus size={12} /> Add Tier
                                </button>
                            </div>

                            <div className="space-y-3">
                                {data.tiers.map((tier, tIdx) => (
                                    <div key={tier.id || `new-${tIdx}`} className="bg-white rounded-[10px] border border-[#e3e4e8] shadow-sm overflow-hidden transition-all hover:border-[#673ab7]/30">
                                        <div className="px-4 py-2 border-b border-[#f1f2f4] bg-[#fafbfc] flex items-center justify-between">
                                            <div className="flex items-center gap-3">
                                                <span className="text-[11px] font-bold text-[#2f3344] uppercase tracking-wider">Tier {tIdx + 1} Price ($)</span>
                                                <input
                                                    type="number"
                                                    value={tier.price}
                                                    onChange={(e) => updateTierPrice(tIdx, e.target.value)}
                                                    className="w-[80px] h-[30px] px-2 bg-white border border-[#e3e4e8] rounded-md focus:border-[#673ab7] outline-none text-[12px] font-bold"
                                                />
                                            </div>
                                            {data.tiers.length > 1 && (
                                                <button
                                                    type="button"
                                                    onClick={() => removeTier(tIdx)}
                                                    className="text-[10px] font-bold text-[#ef4444] hover:bg-red-50 px-2 py-1 rounded-[4px] transition-all"
                                                >
                                                    Remove Tier
                                                </button>
                                            )}
                                        </div>

                                        <div className="p-4">
                                            <div className="space-y-2">
                                                <div className="flex items-center justify-between mb-1">
                                                    <label className="text-[11px] font-bold text-[#727586] uppercase">Features</label>
                                                    <button
                                                        type="button"
                                                        onClick={() => addFeature(tIdx)}
                                                        className="text-[10px] font-bold text-[#673ab7] hover:underline flex items-center gap-1"
                                                    >
                                                        <Plus size={10} /> Add Feature
                                                    </button>
                                                </div>

                                                <div className="space-y-3">
                                                   {tier.features.map((feature, fIdx) => (
                                                       <div key={fIdx} className="p-3 bg-[#fafbfc] border border-[#e3e4e8] rounded-[8px] space-y-2 relative group">
                                                           <div className="flex gap-3 items-start">
                                                               <div className="flex-1 space-y-2">
                                                                   <div className="flex gap-2 items-center">
                                                                       <div className="text-[#a0a3af]"><Type size={14} /></div>
                                                                       <input
                                                                           type="text"
                                                                           value={feature.feature_text}
                                                                           onChange={(e) => updateFeatureText(tIdx, fIdx, e.target.value)}
                                                                           placeholder="Feature Title"
                                                                           className="flex-1 h-[36px] px-3 bg-white border border-[#e3e4e8] rounded-[6px] focus:border-[#673ab7] outline-none transition-all text-[13px] font-medium"
                                                                       />
                                                                       {tier.features.length > 1 && (
                                                                           <button
                                                                               type="button"
                                                                               onClick={() => removeFeature(tIdx, fIdx)}
                                                                               className="w-[32px] h-[32px] flex items-center justify-center text-[#ef4444] hover:bg-red-50 rounded-[4px] transition-all opacity-0 group-hover:opacity-100"
                                                                           >
                                                                               <Trash2 size={14} />
                                                                           </button>
                                                                       )}
                                                                   </div>
                                                                   
                                                                   <div className="pl-6 space-y-1.5 border-l-2 border-[#f1f2f4] ml-2">
                                                                       <div className="flex items-center justify-between mb-1">
                                                                           <span className="text-[10px] font-bold text-[#727586] uppercase tracking-tighter">Sub-Details</span>
                                                                           <button
                                                                               type="button"
                                                                               onClick={() => addSubItem(tIdx, fIdx)}
                                                                               className="text-[10px] font-bold text-[#673ab7] hover:bg-[#673ab7]/5 px-2 py-0.5 rounded flex items-center gap-1 transition-all"
                                                                           >
                                                                               <Plus size={10} /> Add
                                                                           </button>
                                                                       </div>
                                                                       <div className="space-y-1.5">
                                                                           {feature.sub_items.map((subItem, sIdx) => (
                                                                               <div key={sIdx} className="flex gap-2 items-center">
                                                                                   <div className="w-[12px] h-[32px] flex items-center justify-center text-[#ccd0dc]">
                                                                                       <Layers size={10} />
                                                                                   </div>
                                                                                   <input
                                                                                       type="text"
                                                                                       value={subItem}
                                                                                       onChange={(e) => updateSubItemText(tIdx, fIdx, sIdx, e.target.value)}
                                                                                       placeholder="Detail..."
                                                                                       className="flex-1 h-[32px] px-2 bg-white border border-[#e3e4e8] rounded-[4px] focus:border-[#673ab7] outline-none transition-all text-[12px]"
                                                                                   />
                                                                                   <button
                                                                                       type="button"
                                                                                       onClick={() => removeSubItem(tIdx, fIdx, sIdx)}
                                                                                       className="w-[28px] h-[28px] flex items-center justify-center text-[#ef4444] hover:bg-red-50 rounded-[4px]"
                                                                                   >
                                                                                       <Trash2 size={12} />
                                                                                   </button>
                                                                               </div>
                                                                           ))}
                                                                       </div>
                                                                   </div>
                                                               </div>
                                                           </div>
                                                       </div>
                                                   ))}
                                               </div>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* Sidebar */}
                    <div className="col-span-12 lg:col-span-4 space-y-4">
                        <div className="bg-white rounded-[10px] border border-[#e3e4e8] shadow-sm p-4 space-y-4">
                            <div className="space-y-3">
                                <div className="space-y-1">
                                    <label className="text-[11px] font-bold text-[#727586] flex items-center gap-2 uppercase tracking-wider">
                                        <Briefcase size={14} className="text-[#a0a3af]" />
                                        Target Service
                                    </label>
                                    <SelectInput
                                        value={data.service_id}
                                        onChange={(val) => setData("service_id", val)}
                                        options={serviceOptions}
                                        className="w-full"
                                    />
                                    {errors.service_id && <p className="text-red-500 text-[11px] mt-1 font-bold">{errors.service_id}</p>}
                                </div>

                                <div className="flex items-center justify-between p-3 rounded-lg bg-[#fafbfc] border border-[#e3e4e8]">
                                    <span className="text-[12px] font-bold text-[#2f3344] uppercase">Status</span>
                                    <label className="relative inline-flex items-center cursor-pointer">
                                        <input
                                            type="checkbox"
                                            checked={data.status}
                                            onChange={(e) => setData("status", e.target.checked)}
                                            className="sr-only peer"
                                        />
                                        <div className="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#673ab7]"></div>
                                    </label>
                                </div>
                            </div>

                            <div className="space-y-2 pt-4 border-t border-[#f1f2f4]">
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="w-full h-[44px] bg-[#673ab7] hover:bg-[#5e35b1] text-white rounded-[6px] font-bold text-[13px] transition-all shadow-sm flex items-center justify-center disabled:opacity-50"
                                >
                                    {processing ? "Updating..." : "Update Campaign"}
                                </button>
                                <Link
                                    href={route("admin.campaigns.index")}
                                    className="w-full h-[38px] text-[#727586] hover:text-[#2f3344] font-bold text-[12px] transition-all flex items-center justify-center border border-[#e3e4e8] rounded-[6px] hover:bg-gray-50"
                                >
                                    Go Back
                                </Link>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </AdminLayout>
    );
}
