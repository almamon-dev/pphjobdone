import React, { useState, useEffect, useRef } from "react";
import AdminLayout from "@/Layouts/AdminLayout";
import { Head, useForm, router } from "@inertiajs/react";
import {
    MessageCircle,
    Search,
    Send,
    Paperclip,
    FileText,
    Image as ImageIcon,
    User,
    CheckCheck,
    Plus,
    X,
    ExternalLink,
    Phone,
    Video,
    Info,
    Smile,
    ThumbsUp,
    MoreHorizontal,
    Edit2,
    Trash2,
    Save,
    ArrowLeft,
} from "lucide-react";

export default function AdminMessagesIndex({
    conversations = [],
    activeConversationId,
    activeMessages = [],
    activeClient,
    clients = [],
}) {
    const [search, setSearch] = useState("");
    const [isNewChatModalOpen, setIsNewChatModalOpen] = useState(false);
    const [clientSearch, setClientSearch] = useState("");
    const [editingMsgId, setEditingMsgId] = useState(null);
    const [editingText, setEditingText] = useState("");
    const [showMobileChat, setShowMobileChat] = useState(!!activeConversationId);
    const messagesEndRef = useRef(null);

    const { data, setData, post, processing, reset } = useForm({
        conversation_id: activeConversationId || "",
        receiver_id: activeClient?.id || "",
        message: "",
        file: null,
    });

    const scrollToBottom = (behavior = "smooth") => {
        messagesEndRef.current?.scrollIntoView({ behavior });
    };

    useEffect(() => {
        if (activeConversationId) {
            setData((prev) => ({
                ...prev,
                conversation_id: activeConversationId,
                receiver_id: activeClient?.id || prev.receiver_id,
            }));
            // Scroll to bottom when opening a conversation
            setTimeout(() => scrollToBottom("auto"), 50);
        }
    }, [activeConversationId, activeClient]);

    // Live Auto-Refresh (Silent Polling every 3.5 seconds)
    useEffect(() => {
        const interval = setInterval(() => {
            router.reload({
                only: ["conversations", "activeMessages"],
                preserveScroll: true,
            });
        }, 3500);

        return () => clearInterval(interval);
    }, [activeConversationId]);

    const filteredConversations = conversations.filter((convo) => {
        const query = search.toLowerCase();
        const name = convo.other_user?.name?.toLowerCase() || "";
        const email = convo.other_user?.email?.toLowerCase() || "";
        return name.includes(query) || email.includes(query);
    });

    const filteredClients = clients.filter((c) => {
        const query = clientSearch.toLowerCase();
        return c.name?.toLowerCase().includes(query) || c.email?.toLowerCase().includes(query);
    });

    const handleSelectConversation = (convoId) => {
        setShowMobileChat(true);
        router.get(route("admin.messages.index"), { conversation_id: convoId }, { preserveState: true });
    };

    const handleStartNewChatWithClient = (client) => {
        setIsNewChatModalOpen(false);
        setShowMobileChat(true);
        router.get(route("admin.messages.index"), { user_id: client.id });
    };

    const handleSendMessage = (e) => {
        e?.preventDefault();
        if (!data.message.trim() && !data.file) return;

        if (editingMsgId) {
            router.put(
                route("admin.messages.update", editingMsgId),
                { message: data.message },
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        handleCancelEdit();
                    },
                }
            );
        } else {
            post(route("admin.messages.store"), {
                preserveScroll: true,
                onSuccess: () => {
                    reset("message", "file");
                    setTimeout(() => scrollToBottom("smooth"), 100);
                },
            });
        }
    };

    const sendQuickThumbsUp = () => {
        setData("message", "👍");
        setTimeout(() => {
            post(route("admin.messages.store"), {
                preserveScroll: true,
                onSuccess: () => {
                    reset("message");
                    setTimeout(() => scrollToBottom("smooth"), 100);
                },
            });
        }, 100);
    };


    const handleStartEdit = (msg) => {
        setEditingMsgId(msg.id);
        setData("message", msg.message);
    };

    const handleCancelEdit = () => {
        setEditingMsgId(null);
        reset("message");
    };

    const handleDeleteMsg = (msgId) => {
        if (confirm("Are you sure you want to delete this message?")) {
            router.delete(route("admin.messages.destroy", msgId), {
                preserveScroll: true,
            });
        }
    };

    return (
        <AdminLayout>
            <Head title="Messenger - Admin Live Chat" />

            <div className="font-inter max-w-full mx-auto">
                
                {/* Facebook Messenger Container */}
                <div className="grid grid-cols-12 bg-white rounded-2xl border border-slate-200/90 shadow-md overflow-hidden h-[calc(100vh-170px)] min-h-[500px]">

                                       {/* Left Column: FB Messenger Conversations Sidebar */}
                    <div className={`col-span-12 md:col-span-4 border-r border-slate-200 flex flex-col h-full min-h-0 bg-white ${showMobileChat ? "hidden md:flex" : "flex"}`}>
                        
                        {/* Messenger Header */}
                        <div className="p-4 border-b border-slate-100 flex items-center justify-between gap-3 shrink-0">
                            <div className="flex items-center gap-2">
                                <div className="w-10 h-10 rounded-full bg-gradient-to-r from-[#0084ff] to-[#00c6ff] text-white flex items-center justify-center shadow-xs">
                                    <MessageCircle size={22} fill="white" className="text-white" />
                                </div>
                                <h1 className="text-xl font-extrabold text-slate-900 tracking-tight">Chats</h1>
                            </div>

                            <button
                                onClick={() => setIsNewChatModalOpen(true)}
                                className="w-9 h-9 rounded-full bg-[#f0f2f5] hover:bg-[#e4e6eb] text-slate-700 flex items-center justify-center transition-all cursor-pointer"
                                title="New Message"
                            >
                                <Plus size={18} />
                            </button>
                        </div>

                        {/* FB Search Pill */}
                        <div className="px-4 py-2.5 shrink-0">
                            <div className="relative">
                                <Search size={16} className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
                                <input
                                    type="text"
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    placeholder="Search Messenger..."
                                    className="w-full pl-10 pr-4 py-2 bg-[#f0f2f5] border-none rounded-full text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#0084ff]/30 font-medium placeholder-slate-400"
                                />
                            </div>
                        </div>

                        {/* Conversations Stream */}
                        <div className="flex-1 overflow-y-auto min-h-0 px-2 space-y-1 py-1">
                            {filteredConversations.length > 0 ? (
                                filteredConversations.map((convo) => {
                                    const isActive = convo.id === activeConversationId;
                                    const client = convo.other_user;

                                    return (
                                        <button
                                            key={convo.id}
                                            onClick={() => handleSelectConversation(convo.id)}
                                            className={`w-full p-2.5 rounded-xl text-left transition-all flex items-center gap-3 cursor-pointer ${
                                                isActive ? "bg-[#e7f3ff]" : "hover:bg-[#f0f2f5]"
                                            }`}
                                        >
                                            {/* Avatar with Online Badge */}
                                            <div className="relative shrink-0">
                                                {client.avatar ? (
                                                    <img src={client.avatar} alt={client.name} className="w-11 h-11 rounded-full object-cover border border-slate-200" />
                                                ) : (
                                                    <div className="w-11 h-11 rounded-full bg-gradient-to-tr from-[#0084ff] to-[#00c6ff] text-white font-bold flex items-center justify-center text-xs shadow-2xs">
                                                        {client.name.substring(0, 2).toUpperCase()}
                                                    </div>
                                                )}
                                                <span className="absolute bottom-0 right-0 w-3 h-3 bg-[#31a24c] border-2 border-white rounded-full"></span>
                                            </div>

                                            <div className="min-w-0 flex-1">
                                                <div className="flex items-center justify-between gap-1">
                                                    <h4 className={`text-xs truncate ${isActive || convo.unread_count > 0 ? "font-extrabold text-slate-900" : "font-bold text-slate-800"}`}>
                                                        {client.name}
                                                    </h4>
                                                    <span className="text-[10px] text-slate-400 font-medium whitespace-nowrap shrink-0">
                                                        {convo.last_message?.created_at || ""}
                                                    </span>
                                                </div>
                                                <p className={`text-[11px] truncate mt-0.5 ${convo.unread_count > 0 ? "font-bold text-[#0084ff]" : "text-slate-500 font-normal"}`}>
                                                    {convo.last_message?.message || "Started a conversation"}
                                                </p>
                                            </div>

                                            {convo.unread_count > 0 && (
                                                <span className="w-4 h-4 rounded-full bg-[#0084ff] text-white text-[10px] font-bold flex items-center justify-center shrink-0">
                                                    {convo.unread_count}
                                                </span>
                                            )}
                                        </button>
                                    );
                                })
                            ) : (
                                <div className="p-8 text-center text-slate-400 text-xs font-medium">
                                    No messages found. Click <strong className="text-slate-700">+</strong> to message a client.
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Right Column: Active Chat View */}
                    <div className={`col-span-12 md:col-span-8 flex flex-col h-full min-h-0 bg-white ${!showMobileChat ? "hidden md:flex" : "flex"}`}>
                        {activeClient ? (

                            <>
                                {/* Chat Header */}
                                <div className="p-3.5 px-5 border-b border-slate-200 bg-white flex items-center justify-between gap-3 shrink-0 shadow-2xs">
                                    <div className="flex items-center gap-3">
                                        <button
                                            onClick={() => setShowMobileChat(false)}
                                            className="md:hidden p-1.5 rounded-full hover:bg-[#f0f2f5] text-slate-600 transition-all cursor-pointer"
                                            title="Back to Chats"
                                        >
                                            <ArrowLeft size={20} />
                                        </button>
                                        <div className="relative shrink-0">
                                            {activeClient.avatar ? (
                                                <img src={activeClient.avatar} alt={activeClient.name} className="w-10 h-10 rounded-full object-cover border border-slate-200" />
                                            ) : (
                                                <div className="w-10 h-10 rounded-full bg-gradient-to-tr from-[#0084ff] to-[#00c6ff] text-white font-bold flex items-center justify-center text-xs shadow-2xs">
                                                    {activeClient.name?.substring(0, 2).toUpperCase()}
                                                </div>
                                            )}
                                            <span className="absolute bottom-0 right-0 w-3 h-3 bg-[#31a24c] border-2 border-white rounded-full"></span>
                                        </div>
                                        <div>
                                            <h3 className="text-sm font-extrabold text-slate-900 leading-tight">{activeClient.name}</h3>
                                            <p className="text-[11px] text-[#31a24c] font-bold flex items-center gap-1">
                                                <span className="w-1.5 h-1.5 rounded-full bg-[#31a24c] inline-block animate-pulse"></span> Active Now
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {/* Messages Stream */}
                                <div className="flex-1 p-5 overflow-y-auto space-y-3 bg-[#f8fafc]">
                                    {activeMessages.length > 0 ? (
                                        activeMessages.map((msg) => (
                                            <div
                                                key={msg.id}
                                                className={`flex flex-col group ${msg.is_me ? "items-end" : "items-start"}`}
                                            >
                                                <div className={`flex items-center gap-2 max-w-[80%] ${msg.is_me ? "flex-row-reverse" : "flex-row"}`}>
                                                    {!msg.is_me && (
                                                        <div className="w-7 h-7 rounded-full bg-[#0084ff]/20 text-[#0084ff] text-[10px] font-bold flex items-center justify-center shrink-0 mb-1">
                                                            {activeClient.name?.substring(0, 1).toUpperCase()}
                                                        </div>
                                                    )}
                                                    
                                                    {/* Hover Action Buttons */}
                                                    {msg.is_me && (
                                                        <div className="opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1 shrink-0">
                                                            {!msg.file_path && (
                                                                <button
                                                                    type="button"
                                                                    onClick={() => handleStartEdit(msg)}
                                                                    className="p-1.5 rounded-full text-slate-400 hover:text-[#0084ff] hover:bg-slate-100 transition-all cursor-pointer"
                                                                    title="Edit Message"
                                                                >
                                                                    <Edit2 size={13} />
                                                                </button>
                                                            )}
                                                            <button
                                                                type="button"
                                                                onClick={() => handleDeleteMsg(msg.id)}
                                                                className="p-1.5 rounded-full text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-all cursor-pointer"
                                                                title="Delete Message"
                                                            >
                                                                <Trash2 size={13} />
                                                            </button>
                                                        </div>
                                                    )}

                                                    <div
                                                        className={`p-3 px-4 rounded-[18px] text-xs font-inter leading-relaxed transition-all ${
                                                            msg.is_me
                                                                ? editingMsgId === msg.id
                                                                    ? "bg-[#0084ff] text-white rounded-br-[4px] ring-2 ring-offset-1 ring-[#0084ff]"
                                                                    : "bg-gradient-to-r from-[#0084ff] to-[#00c6ff] text-white rounded-br-[4px] shadow-2xs"
                                                                : "bg-white text-[#050505] border border-slate-200/80 rounded-bl-[4px] shadow-2xs"
                                                        }`}
                                                    >
                                                        {msg.file_path && (
                                                            <div className="mb-2">
                                                                {msg.type === "image" ? (
                                                                    <img src={msg.file_path} alt="Attachment" className="max-w-xs rounded-xl border border-white/20" />
                                                                ) : (
                                                                    <a
                                                                        href={msg.file_path}
                                                                        target="_blank"
                                                                        rel="noreferrer"
                                                                        className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-black/10 text-xs font-bold hover:underline"
                                                                    >
                                                                        <FileText size={14} /> Attachment File
                                                                    </a>
                                                                )}
                                                            </div>
                                                        )}
                                                        <p className="whitespace-pre-wrap">{msg.message}</p>
                                                    </div>
                                                </div>

                                                <div className={`flex items-center gap-1 mt-1 px-1 ${msg.is_me ? "justify-end" : "justify-start"}`}>
                                                    <span className="text-[10px] text-slate-400 font-medium">
                                                        {msg.created_at}
                                                    </span>
                                                    {msg.is_me && <CheckCheck size={12} className="text-[#0084ff]" />}
                                                </div>
                                            </div>
                                        ))
                                    ) : (
                                        <div className="flex flex-col items-center justify-center h-full text-slate-400">
                                            <div className="w-16 h-16 rounded-full bg-gradient-to-tr from-[#0084ff] to-[#00c6ff] text-white flex items-center justify-center text-2xl font-bold shadow-md mb-3">
                                                {activeClient.name?.substring(0, 1).toUpperCase()}
                                            </div>
                                            <h3 className="text-sm font-extrabold text-slate-900 mb-1">{activeClient.name}</h3>
                                            <p className="text-xs text-slate-500 font-medium">You are connected on Messenger. Say hello!</p>
                                        </div>
                                    )}
                                    <div ref={messagesEndRef} />
                                </div>

                                {/* Input Footer */}
                                <form onSubmit={handleSendMessage} className="p-3 px-4 border-t border-slate-100 bg-white shrink-0">
                                    {editingMsgId && (
                                        <div className="mb-2.5 p-2 px-3.5 bg-[#e7f3ff] border border-[#0084ff]/20 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs font-medium text-[#0084ff]">
                                            <div className="flex items-center gap-2 truncate">
                                                <Edit2 size={14} className="shrink-0" />
                                                <span className="font-bold">Editing message:</span>
                                                <span className="truncate italic opacity-85">"{data.message}"</span>
                                            </div>
                                            <button
                                                type="button"
                                                onClick={handleCancelEdit}
                                                className="text-slate-400 hover:text-slate-700 p-1 rounded-full hover:bg-[#0084ff]/10 shrink-0 cursor-pointer"
                                                title="Cancel Edit"
                                            >
                                                <X size={14} />
                                            </button>
                                        </div>
                                    )}

                                    {data.file && (
                                        <div className="mb-2 p-2 bg-[#f0f2f5] rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                                            <span className="truncate font-medium text-slate-700">{data.file.name}</span>
                                            <button type="button" onClick={() => setData("file", null)} className="text-rose-500 hover:text-rose-700">
                                                <X size={14} />
                                            </button>
                                        </div>
                                    )}

                                    <div className="flex items-center gap-2">
                                        <label className="w-9 h-9 rounded-full text-[#0084ff] hover:bg-[#f0f2f5] flex items-center justify-center cursor-pointer transition-all shrink-0" title="Attach File">
                                            <Paperclip size={20} />
                                            <input
                                                type="file"
                                                className="hidden"
                                                onChange={(e) => setData("file", e.target.files[0])}
                                            />
                                        </label>

                                        <input
                                            type="text"
                                            value={data.message}
                                            onChange={(e) => setData("message", e.target.value)}
                                            placeholder="Aa"
                                            className="flex-1 px-4 py-2.5 bg-[#f0f2f5] border-none rounded-full text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#0084ff]/40 transition-all font-medium placeholder-slate-400"
                                        />

                                        {data.message.trim() || data.file ? (
                                            <button
                                                type="submit"
                                                disabled={processing}
                                                className="w-9 h-9 rounded-full bg-[#0084ff] hover:bg-[#0073e6] text-white flex items-center justify-center transition-all shadow-2xs disabled:opacity-50 shrink-0 cursor-pointer"
                                                title="Send"
                                            >
                                                <Send size={16} className="translate-x-0.5" />
                                            </button>
                                        ) : (
                                            <button
                                                type="button"
                                                onClick={sendQuickThumbsUp}
                                                className="w-9 h-9 rounded-full text-[#0084ff] hover:bg-[#f0f2f5] flex items-center justify-center transition-all shrink-0 cursor-pointer"
                                                title="Send Like"
                                            >
                                                <ThumbsUp size={20} />
                                            </button>
                                        )}
                                    </div>
                                </form>
                            </>
                        ) : (
                            <div className="flex flex-col items-center justify-center h-full text-slate-400 p-8 text-center bg-white">
                                <div className="w-20 h-20 rounded-full bg-[#e7f3ff] text-[#0084ff] flex items-center justify-center mb-4">
                                    <MessageCircle size={40} fill="#0084ff" />
                                </div>
                                <h3 className="text-base font-extrabold text-slate-900 mb-1">Select a Chat to Start Messaging</h3>
                                <p className="text-xs text-slate-500 max-w-sm">
                                    Search for a client from your active Messenger chat list or click + to start a new chat.
                                </p>
                            </div>
                        )}
                    </div>
                </div>

                {/* Start New Chat Modal */}
                {isNewChatModalOpen && (
                    <div className="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-50 flex items-center justify-center p-4">
                        <div className="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full p-5 font-inter">
                            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                                <h3 className="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                                    <MessageCircle size={18} className="text-[#0084ff]" />
                                    New Message
                                </h3>
                                <button onClick={() => setIsNewChatModalOpen(false)} className="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-700">
                                    <X size={16} />
                                </button>
                            </div>

                            <div className="relative mb-3">
                                <Search size={15} className="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
                                <input
                                    type="text"
                                    value={clientSearch}
                                    onChange={(e) => setClientSearch(e.target.value)}
                                    placeholder="Type name or email..."
                                    className="w-full pl-9 pr-3 py-2 bg-[#f0f2f5] border-none rounded-full text-xs focus:outline-none focus:ring-2 focus:ring-[#0084ff]/40"
                                />
                            </div>

                            <div className="max-h-64 overflow-y-auto space-y-1">
                                {filteredClients.length > 0 ? (
                                    filteredClients.map((client) => (
                                        <button
                                            key={client.id}
                                            onClick={() => handleStartNewChatWithClient(client)}
                                            className="w-full p-2.5 text-left flex items-center gap-3 hover:bg-[#f0f2f5] rounded-xl transition-all cursor-pointer"
                                        >
                                            <div className="w-10 h-10 rounded-full bg-gradient-to-tr from-[#0084ff] to-[#00c6ff] text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-2xs">
                                                {client.name.substring(0, 2).toUpperCase()}
                                            </div>
                                            <div className="min-w-0 flex-1">
                                                <h4 className="text-xs font-bold text-slate-900 truncate">{client.name}</h4>
                                                <p className="text-[10px] text-slate-500 truncate">{client.email}</p>
                                            </div>
                                        </button>
                                    ))
                                ) : (
                                    <p className="p-4 text-center text-slate-400 text-xs">No users found matching your search.</p>
                                )}
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </AdminLayout>
    );
}