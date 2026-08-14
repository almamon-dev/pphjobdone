import React, { useState, useRef, useEffect } from "react";
import { router, usePage, Link } from "@inertiajs/react";
import axios from "axios";
import {
    Search,
    Bell,
    Calendar,
    Menu,
    ChevronDown,
    LogOut,
    Settings,
    Home,
    Loader2,
    X,
    Briefcase,
    CircleDollarSign,
    Zap,
    Users,
    FolderKanban,
    Mail,
    ArrowRight,
    Plus,
    CreditCard,
} from "lucide-react";

const quickLinks = [
    { title: "Dashboard Overview", category: "Navigation", url: "/dashboard", icon: Home },
    { title: "Services Management", category: "Services", url: "/admin/services", icon: Briefcase },
    { title: "Add New Service", category: "Services", url: "/admin/services/create", icon: Plus },
    { title: "Pricing Plans", category: "Plans", url: "/admin/pricing-plans", icon: CircleDollarSign },
    { title: "Create Pricing Plan", category: "Plans", url: "/admin/pricing-plans/create", icon: Plus },
    { title: "Campaigns Management", category: "Campaigns", url: "/admin/campaigns", icon: Zap },
    { title: "Create Campaign", category: "Campaigns", url: "/admin/campaigns/create", icon: Plus },
    { title: "Client Bookings & Tasks", category: "Bookings", url: "/admin/bookings", icon: FolderKanban },
    { title: "Contact Messages", category: "Contacts", url: "/admin/contacts", icon: Mail },
    { title: "User Management", category: "Users", url: "/admin/users", icon: Users },
    { title: "System Settings", category: "Settings", url: "/admin/settings/system", icon: Settings },
    { title: "Payment Settings", category: "Settings", url: "/admin/settings/payment", icon: CreditCard },
];

const iconMap = {
    Briefcase: Briefcase,
    CircleDollarSign: CircleDollarSign,
    Zap: Zap,
    Users: Users,
    Mail: Mail,
    FolderKanban: FolderKanban,
    Settings: Settings,
    CreditCard: CreditCard,
    Home: Home,
    Plus: Plus,
};

const getPageTitle = (url) => {
    const path = url.split("?")[0];
    if (path === "/dashboard") return "Dashboard";
    if (path.startsWith("/admin/services")) return "Services";
    if (path.startsWith("/admin/pricing-plans")) return "Pricing Plans";
    if (path.startsWith("/admin/campaigns")) return "Campaigns";
    if (path.startsWith("/admin/bookings")) return "Client Bookings";
    if (path.startsWith("/admin/contacts")) return "Contacts";
    if (path.startsWith("/admin/users")) return "Users";
    if (path.startsWith("/admin/settings/system")) return "System Settings";
    if (path.startsWith("/admin/settings/payment")) return "Payment Settings";
    return "Dashboard";
};

const getInitials = (name) => {
    if (!name) return "SA";
    const parts = name.trim().split(" ");
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }
    return name.slice(0, 2).toUpperCase();
};

const Header = ({ onMenuClick }) => {
    const { url, props } = usePage();
    const { auth, adminNotifications } = props;
    const [open, setOpen] = useState(false);
    const [notifOpen, setNotifOpen] = useState(false);
    
    // Search State
    const [searchOpen, setSearchOpen] = useState(false);
    const [searchQuery, setSearchQuery] = useState("");
    const [isSearching, setIsSearching] = useState(false);
    const [dbResults, setDbResults] = useState([]);

    const dropdownRef = useRef(null);
    const notifRef = useRef(null);
    const searchRef = useRef(null);
    const inputRef = useRef(null);

    const pageTitle = getPageTitle(url);
    const userInitials = getInitials(auth?.user?.name || "School Admin");

    const handleLogout = () => {
        router.post(route("logout"));
    };

    const markAsRead = (id) => {
        router.post(route("notifications.read", id), {}, { preserveScroll: true, preserveState: true });
    };

    // Keyboard shortcut (Ctrl + K / Cmd + K / ESC)
    useEffect(() => {
        const handleKeyDown = (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === "k") {
                e.preventDefault();
                setSearchOpen(true);
                setTimeout(() => inputRef.current?.focus(), 50);
            } else if (e.key === "Escape") {
                setSearchOpen(false);
            }
        };
        window.addEventListener("keydown", handleKeyDown);
        return () => window.removeEventListener("keydown", handleKeyDown);
    }, []);

    // Debounced Backend Search API call
    useEffect(() => {
        if (!searchQuery || searchQuery.trim().length < 2) {
            setDbResults([]);
            setIsSearching(false);
            return;
        }

        setIsSearching(true);
        const timer = setTimeout(() => {
            axios
                .get(route("admin.global-search"), { params: { q: searchQuery } })
                .then((res) => {
                    setDbResults(res.data.results || []);
                })
                .catch(() => {
                    setDbResults([]);
                })
                .finally(() => {
                    setIsSearching(false);
                });
        }, 250);

        return () => clearTimeout(timer);
    }, [searchQuery]);

    // Close dropdowns on outside click
    useEffect(() => {
        const handleClickOutside = (e) => {
            if (dropdownRef.current && !dropdownRef.current.contains(e.target)) setOpen(false);
            if (notifRef.current && !notifRef.current.contains(e.target)) setNotifOpen(false);
            if (searchRef.current && !searchRef.current.contains(e.target)) setSearchOpen(false);
        };
        document.addEventListener("mousedown", handleClickOutside);
        return () => document.removeEventListener("mousedown", handleClickOutside);
    }, []);

    // Filter local quick links
    const filteredQuickLinks = searchQuery.trim()
        ? quickLinks.filter(
              (item) =>
                  item.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
                  item.category.toLowerCase().includes(searchQuery.toLowerCase())
          )
        : quickLinks;

    const handleSelectResult = (url) => {
        setSearchOpen(false);
        setSearchQuery("");
        router.visit(url);
    };

    return (
        <header className="h-[64px] bg-white border-b border-slate-200/80 sticky top-0 z-50 flex items-center justify-between px-4 md:px-6 shadow-2xs">
            {/* LEFT: Toggle, Page Title & Pill Search Bar */}
            <div className="flex items-center gap-3 md:gap-6 flex-1 min-w-0">
              
                <h1 className="text-base md:text-lg font-bold text-[#1e293b] tracking-tight shrink-0">
                    {pageTitle}
                </h1>

                {/* Pill Search Bar (Placed right next to Page Title) */}
                <div className="w-[240px] sm:w-[280px] md:w-[320px] lg:w-[360px] relative" ref={searchRef}>
                    <div className="relative group">
                        <div className="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-[#0a66c2] transition-colors pointer-events-none">
                            {isSearching ? (
                                <Loader2 size={16} className="animate-spin text-[#0a66c2]" />
                            ) : (
                                <Search size={16} strokeWidth={2} />
                            )}
                        </div>
                        <input
                            ref={inputRef}
                            type="text"
                            value={searchQuery}
                            onFocus={() => setSearchOpen(true)}
                            onChange={(e) => {
                                setSearchQuery(e.target.value);
                                setSearchOpen(true);
                            }}
                            placeholder="Search anything..."
                            className="w-full bg-[#f1f4f8] hover:bg-[#ebf0f5] border border-slate-200/80 focus:border-[#0a66c2] rounded-full py-2 pl-10 pr-8 text-xs md:text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0a66c2]/15 focus:bg-white transition-all placeholder:text-slate-400"
                        />
                        {searchQuery && (
                            <button
                                onClick={() => {
                                    setSearchQuery("");
                                    setDbResults([]);
                                    inputRef.current?.focus();
                                }}
                                className="absolute right-3 top-1/2 -translate-y-1/2 p-1 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-200 transition-colors"
                            >
                                <X size={14} />
                            </button>
                        )}
                    </div>

                {/* SEARCH RESULTS DROPDOWN (COMPACT & SCROLLBAR HIDDEN) */}
                {searchOpen && (
                    <div className="absolute left-0 right-0 mt-1.5 bg-white rounded-xl shadow-xl border border-slate-200 overflow-hidden z-50 max-h-[300px] flex flex-col animate-in fade-in slide-in-from-top-1 duration-150">
                        <div className="overflow-y-auto p-1.5 divide-y divide-slate-100 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
                            {/* Database Live Results */}
                            {dbResults.length > 0 && (
                                <div className="pb-1">
                                    <div className="px-2 py-1 text-[10px] font-extrabold text-[#0a66c2] uppercase tracking-wider">
                                        Database Matches
                                    </div>
                                    <div className="space-y-0.5">
                                        {dbResults.map((item, index) => {
                                            const IconComp = iconMap[item.icon] || Search;
                                            return (
                                                <button
                                                    key={`db-${index}`}
                                                    onClick={() => handleSelectResult(item.url)}
                                                    className="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-left hover:bg-slate-100/80 transition-all group"
                                                >
                                                    <div className="w-6 h-6 rounded-md bg-[#0a66c2]/10 text-[#0a66c2] flex items-center justify-center shrink-0">
                                                        <IconComp size={13} />
                                                    </div>
                                                    <div className="flex-1 min-w-0">
                                                        <p className="text-[12px] font-bold text-slate-800 truncate group-hover:text-[#0a66c2] transition-colors leading-tight">
                                                            {item.title}
                                                        </p>
                                                        <p className="text-[10px] text-slate-400 truncate leading-tight">
                                                            {item.subtitle}
                                                        </p>
                                                    </div>
                                                    <span className="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-slate-100 text-slate-600 border border-slate-200/60 shrink-0">
                                                        {item.type}
                                                    </span>
                                                </button>
                                            );
                                        })}
                                    </div>
                                </div>
                            )}

                            {/* Quick Links & Pages */}
                            {filteredQuickLinks.length > 0 && (
                                <div className={dbResults.length > 0 ? "pt-1" : ""}>
                                    <div className="px-2 py-1 text-[10px] font-extrabold text-slate-400">
                                        Pages & Actions
                                    </div>
                                    <div className="space-y-0.5">
                                        {filteredQuickLinks.map((item, index) => {
                                            const IconComp = item.icon;
                                            return (
                                                <button
                                                    key={`ql-${index}`}
                                                    onClick={() => handleSelectResult(item.url)}
                                                    className="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-left hover:bg-slate-100/80 transition-all group"
                                                >
                                                    <div className="w-6 h-6 rounded-md bg-slate-100 text-slate-600 group-hover:bg-[#0a66c2]/10 group-hover:text-[#0a66c2] flex items-center justify-center shrink-0 transition-colors">
                                                        <IconComp size={13} />
                                                    </div>
                                                    <div className="flex-1 min-w-0">
                                                        <p className="text-[12px] font-semibold text-slate-700 group-hover:text-slate-900 truncate leading-tight">
                                                            {item.title}
                                                        </p>
                                                    </div>
                                                    <span className="text-[10px] text-slate-400 font-medium group-hover:text-[#0a66c2] flex items-center gap-1 shrink-0">
                                                        {item.category}
                                                        <ArrowRight size={11} className="opacity-0 group-hover:opacity-100 transition-opacity" />
                                                    </span>
                                                </button>
                                            );
                                        })}
                                    </div>
                                </div>
                            )}

                            {/* Empty State */}
                            {searchQuery && !isSearching && dbResults.length === 0 && filteredQuickLinks.length === 0 && (
                                <div className="p-5 text-center text-slate-500">
                                    <Search size={22} className="mx-auto mb-1 opacity-30 text-slate-400" />
                                    <p className="text-xs font-bold text-slate-700">No results found</p>
                                    <p className="text-[11px] text-slate-400 mt-0.5">
                                        No items matching "{searchQuery}".
                                    </p>
                                </div>
                            )}
                        </div>
                    </div>
                )}
            </div>
        </div>

        {/* RIGHT: Notifications, Calendar & Profile */}
            <div className="flex items-center gap-2 md:gap-3">
                {/* Notifications Bell Icon */}
                <div className="relative" ref={notifRef}>
                    <button
                        onClick={() => setNotifOpen(!notifOpen)}
                        className={`p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-full transition-all relative ${notifOpen ? "bg-slate-100 text-[#0a66c2]" : ""}`}
                        title="Notifications"
                    >
                        <Bell size={19} strokeWidth={1.8} />
                        {adminNotifications?.unreadCount > 0 && (
                            <span className="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
                        )}
                    </button>

                    {/* Notification Dropdown */}
                    {notifOpen && (
                        <div className="absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden z-50 animate-in fade-in slide-in-from-top-2 duration-200">
                            <div className="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                                <h3 className="font-bold text-slate-800 text-sm">Notifications</h3>
                                {adminNotifications?.unreadCount > 0 && (
                                    <span className="bg-red-100 text-red-600 text-xs font-bold px-2 py-0.5 rounded-full">
                                        {adminNotifications.unreadCount} New
                                    </span>
                                )}
                            </div>
                            <div className="max-h-[300px] overflow-y-auto no-scrollbar">
                                {adminNotifications?.list?.length > 0 ? (
                                    adminNotifications.list.map((notif) => (
                                        <div key={notif.id} className="p-4 border-b border-slate-50 hover:bg-slate-50 transition-colors flex gap-3 relative group">
                                            <div className="w-8 h-8 rounded-full bg-[#0a66c2]/10 flex items-center justify-center shrink-0 mt-1">
                                                <Mail size={14} className="text-[#0a66c2]" />
                                            </div>
                                            <div className="flex-1 pr-4">
                                                <p className="text-[13px] text-slate-800 leading-tight">
                                                    <span className="font-bold">{notif.data.name}</span> sent a message.
                                                </p>
                                                <p className="text-[11px] text-slate-500 mt-1 truncate">{notif.data.message}</p>
                                            </div>
                                            <button
                                                onClick={() => markAsRead(notif.id)}
                                                className="absolute right-3 top-4 opacity-0 group-hover:opacity-100 transition-opacity text-[10px] text-[#0a66c2] font-semibold hover:underline bg-white/80 px-1 rounded"
                                            >
                                                Mark Read
                                            </button>
                                        </div>
                                    ))
                                ) : (
                                    <div className="p-8 text-center text-slate-500 text-[13px]">
                                        <Bell size={24} className="mx-auto mb-2 opacity-20" />
                                        No new notifications
                                    </div>
                                )}
                            </div>
                            <div className="p-2 bg-slate-50 border-t border-slate-100">
                                <Link href={route('admin.contacts.index')} className="block text-center text-xs text-[#0a66c2] font-bold hover:underline py-1">
                                    View All Contacts
                                </Link>
                            </div>
                        </div>
                    )}
                </div>

                {/* Vertical Divider */}
                <div className="h-6 w-[1px] bg-slate-200 mx-1" />

                {/* User Profile Dropdown */}
                <div className="relative" ref={dropdownRef}>
                    <button
                        onClick={() => setOpen(!open)}
                        className="flex items-center gap-2.5 p-1 rounded-full hover:bg-slate-100 transition-colors"
                    >
                        {auth?.user?.profile_photo_url ? (
                            <img
                                src={auth.user.profile_photo_url}
                                alt="User Avatar"
                                className="w-8 h-8 md:w-9 md:h-9 rounded-full object-cover shadow-2xs border border-slate-200"
                            />
                        ) : (
                            <div className="w-8 h-8 md:w-9 md:h-9 rounded-full bg-[#dcfce7] text-[#15803d] font-bold text-xs md:text-sm flex items-center justify-center border border-[#bbf7d0] shrink-0">
                                {userInitials}
                            </div>
                        )}
                        
                        <div className="hidden sm:block text-left leading-tight">
                            <p className="text-xs md:text-sm font-bold text-[#1e293b] leading-none">
                                {auth?.user?.name || "School Admin"}
                            </p>
                            <p className="text-[10px] md:text-[11px] text-slate-400 font-medium mt-0.5">
                                Super Admin
                            </p>
                        </div>
                        <ChevronDown
                            size={14}
                            className={`text-slate-400 transition-transform duration-300 ${open ? "rotate-180" : ""}`}
                        />
                    </button>

                    {/* Profile Dropdown Menu */}
                    {open && (
                        <div className="absolute right-0 mt-3 w-64 bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden z-50 animate-in fade-in slide-in-from-top-2 duration-200">
                            <div className="p-4 bg-slate-50 border-b border-slate-100 flex items-center gap-3">
                                {auth?.user?.profile_photo_url ? (
                                    <img
                                        src={auth.user.profile_photo_url}
                                        className="w-10 h-10 rounded-full"
                                        alt="Avatar"
                                    />
                                ) : (
                                    <div className="w-10 h-10 rounded-full bg-[#dcfce7] text-[#15803d] font-bold text-sm flex items-center justify-center border border-[#bbf7d0]">
                                        {userInitials}
                                    </div>
                                )}
                                <div className="overflow-hidden">
                                    <p className="text-sm font-bold text-slate-900 truncate">
                                        {auth?.user?.name || "School Admin"}
                                    </p>
                                    <Link
                                        href={route("profile.edit")}
                                        className="text-[11px] text-[#0a66c2] font-semibold hover:underline"
                                    >
                                        Manage Account
                                    </Link>
                                </div>
                            </div>

                            <div className="p-2">
                                <DropdownLink
                                    icon={Settings}
                                    label="General Settings"
                                    href={route("admin.settings.system")}
                                />
                                <DropdownLink
                                    icon={Home}
                                    label="Dashboard"
                                    href={route("dashboard")}
                                />
                            </div>

                            <div className="p-2 border-t border-slate-100 bg-slate-50/50">
                                <button
                                    onClick={handleLogout}
                                    className="w-full text-left px-3 py-2.5 text-[13px] text-red-500 hover:bg-red-50 font-bold flex items-center gap-3 rounded-xl transition-colors"
                                >
                                    <LogOut size={16} />
                                    <span>Sign Out</span>
                                </button>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </header>
    );
};

const DropdownLink = ({ icon: Icon, label, href }) => (
    <Link
        href={href}
        className="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all font-semibold group"
    >
        <Icon
            size={17}
            className="text-slate-400 group-hover:text-[#0a66c2] transition-colors"
        />
        <span>{label}</span>
    </Link>
);

export default Header;
