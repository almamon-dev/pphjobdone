import React, { useState, useEffect } from "react";
import Header from "../Components/Navigation/Admin/Header";
import Sidebar from "../Components/Navigation/Admin/Sidebar";

export default function AdminLayout({ children }) {
    const [isMobileOpen, setIsMobileOpen] = useState(false);
    const [isCollapsed, setIsCollapsed] = useState(false); // Initially open/expanded
    const [isDesktop, setIsDesktop] = useState(true);

    useEffect(() => {
        const checkDesktop = () => {
            setIsDesktop(window.innerWidth >= 1024);
        };
        checkDesktop();
        window.addEventListener("resize", checkDesktop);
        return () => window.removeEventListener("resize", checkDesktop);
    }, []);

    // Constants for width
    const expandedWidth = "260px";
    const collapsedWidth = "90px";

    return (
        <div className="flex h-screen bg-[#f8fafc] overflow-hidden">
            {/* Mobile Overlay */}
            {isMobileOpen && (
                <div
                    className="fixed inset-0 bg-slate-900/40 z-[155] lg:hidden backdrop-blur-sm transition-opacity"
                    onClick={() => setIsMobileOpen(false)}
                />
            )}

            {/* Dynamic Sidebar Container (Fixed Screen Height - Never Stretches) */}
            <aside
                style={{ width: isDesktop ? (isCollapsed ? collapsedWidth : expandedWidth) : expandedWidth }}
                className={`fixed inset-y-0 left-0 h-screen z-[160] bg-white transition-all duration-300 ease-in-out border-r border-slate-200/60
                    ${isMobileOpen ? "translate-x-0" : "-translate-x-full lg:translate-x-0"}`}
            >

                <Sidebar
                    isCollapsed={isDesktop ? isCollapsed : false}
                    toggleCollapse={() => setIsCollapsed(!isCollapsed)}
                />
            </aside>

            <div
                className="flex-1 flex flex-col min-w-0 transition-all duration-300 ease-in-out"
                style={{
                    marginLeft: isDesktop ? (isCollapsed ? collapsedWidth : expandedWidth) : "0px",
                }}
            >
                <Header onMenuClick={() => setIsMobileOpen(true)} />
                <main className="flex-1 overflow-y-auto no-scrollbar">
                    <div className="p-4 md:p-6 lg:p-8 max-w-[1600px] mx-auto w-full">
                        {children}
                    </div>
                </main>
            </div>
        </div>
    );
}
