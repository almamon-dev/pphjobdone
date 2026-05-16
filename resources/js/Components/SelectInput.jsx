import React from "react";
import { ChevronDown } from "lucide-react";

export default function SelectInput({ value, onChange, options, className = "" }) {
    return (
        <div className={`relative ${className}`}>
            <select
                value={value}
                onChange={(e) => onChange(e.target.value)}
                className="h-[44px] pl-4 pr-10 bg-[#f8f9fc] border border-[#e3e4e8] rounded-[8px] text-[14px] text-[#2f3344] font-medium focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 transition-all appearance-none cursor-pointer w-full"
            >
                {options.map((opt) => (
                    <option key={opt.value} value={opt.value}>
                        {opt.label}
                    </option>
                ))}
            </select>
            <div className="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[#727586]">
                <ChevronDown size={14} />
            </div>
        </div>
    );
}
