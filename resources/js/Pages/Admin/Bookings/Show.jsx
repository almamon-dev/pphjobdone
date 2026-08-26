import React, { useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import { ChevronLeft, FolderKanban, Plus, Trash2, Edit2, Save, X, Calendar, Home, CheckCircle, Globe, Tag, ExternalLink, Link2, User, Layers, DollarSign, ChevronDown, Mail, MessageSquare } from 'lucide-react';

export default function BookingsShow({ booking }) {
    const [isAddingTask, setIsAddingTask] = useState(false);
    const [editingTaskId, setEditingTaskId] = useState(null);

    const { data: newTask, setData: setNewTask, post, processing: adding, reset } = useForm({
        title: '',
        description: '',
        progress: 0,
        status: 'pending',
        due_date: '',
    });

    const { data: editTaskData, setData: setEditTaskData, put, processing: saving, reset: resetEdit } = useForm({
        title: '',
        description: '',
        progress: 0,
        status: 'pending',
        due_date: '',
    });

    const handleAddTask = (e) => {
        e.preventDefault();
        post(route('admin.bookings.tasks.store', booking.id), {
            onSuccess: () => {
                setIsAddingTask(false);
                reset();
            }
        });
    };

    const startEditing = (task) => {
        setEditingTaskId(task.id);
        setEditTaskData({
            title: task.title,
            description: task.description || '',
            progress: task.progress,
            status: task.status,
            due_date: task.due_date ? new Date(task.due_date).toISOString().split('T')[0] : '',
        });
    };

    const handleUpdateTask = (e, taskId) => {
        e.preventDefault();
        put(route('admin.bookings.tasks.update', [booking.id, taskId]), {
            onSuccess: () => {
                setEditingTaskId(null);
            }
        });
    };

    const handleQuickProgressUpdate = (task, newProgress) => {
        const targetStatus = newProgress >= 100 ? 'completed' : (newProgress > 0 ? 'ongoing' : 'pending');
        router.put(route('admin.bookings.tasks.update', [booking.id, task.id]), {
            title: task.title,
            description: task.description || '',
            progress: newProgress,
            status: targetStatus,
            due_date: task.due_date ? new Date(task.due_date).toISOString().split('T')[0] : '',
        }, {
            preserveScroll: true,
        });
    };

    const handleDeleteTask = (taskId) => {
        if (confirm('Are you sure you want to delete this task?')) {
            router.delete(route('admin.bookings.tasks.destroy', [booking.id, taskId]));
        }
    };

    // Extract target url and keywords from direct columns or fallback campaign_details
    const websiteUrl = booking.website_url || booking.campaign_details?.website_url || booking.campaign_details?.links || null;
    const targetKeywords = booking.target_keywords || booking.campaign_details?.target_keywords || booking.campaign_details?.keywords || null;

    const totalTasks = booking.tasks.length;
    const overallProgress = totalTasks > 0 ? Math.round(booking.tasks.reduce((acc, t) => acc + Number(t.progress || 0), 0) / totalTasks) : 0;

    return (
        <AdminLayout>
            <Head title={`Booking BKG-${booking.id}`} />

            <div className="space-y-4 max-w-full mx-auto pb-16 font-inter">
                {/* Compact Top Navigation & Action Header */}
                <div className="flex flex-wrap items-center justify-between gap-3 bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-2xs">
                    <div className="flex items-center gap-3">
                        <Link href="/admin/bookings" className="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-200 text-slate-600 hover:bg-purple-50 hover:text-purple-600 hover:border-purple-200 transition-all">
                            <ChevronLeft size={18} />
                        </Link>
                        <div>
                            <div className="flex items-center gap-2">
                                <h1 className="text-[16px] font-bold text-slate-800 tracking-tight flex items-center gap-2">
                                    <span>BKG-{booking.id}</span>
                                    <span className="text-slate-300 font-normal">|</span>
                                    <span className="text-purple-700">{booking.plan_name || booking.service?.title || 'Service Order'}</span>
                                </h1>
                                <span className={`inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider border ${booking.status === 'active' || booking.status === 'ongoing' ? 'bg-purple-50 text-purple-700 border-purple-200' :
                                    booking.status === 'completed' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200'}`}>
                                    {booking.status || 'Active'}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div className="flex items-center gap-2">
                        <button
                            onClick={() => setIsAddingTask(!isAddingTask)}
                            className={`px-3.5 py-1.5 rounded-lg text-[12px] font-bold transition-all flex items-center gap-1.5 shadow-2xs ${isAddingTask
                                ? 'bg-slate-100 text-slate-700 border border-slate-300 hover:bg-slate-200'
                                : 'bg-purple-600 text-white hover:bg-purple-700 shadow-purple-200'
                                }`}
                        >
                            {isAddingTask ? <X size={15} /> : <Plus size={15} />}
                            {isAddingTask ? 'Close Form' : 'Add Task'}
                        </button>
                    </div>
                </div>

                {/* Ultra-Compact Client & Order Specs Banner */}
                <div className="bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-950 text-white rounded-xl p-4 shadow-sm relative overflow-hidden">
                    <div className="absolute right-0 top-0 translate-x-4 -translate-y-4 w-40 h-40 bg-purple-500/10 rounded-full blur-2xl pointer-events-none"></div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 relative z-10 items-center">
                        {/* Client Info & Direct Communication */}
                        <div className="flex items-center gap-3">
                            <div className="w-9 h-9 rounded-lg bg-white/10 border border-white/15 flex items-center justify-center text-purple-300 shrink-0">
                                <User size={18} />
                            </div>
                            <div className="min-w-0">
                                <p className="text-[10px] text-slate-400 font-semibold">Client</p>
                                <p className="text-[13px] font-bold text-white truncate">{booking.user?.name || 'Guest Client'}</p>
                                <div className="flex items-center gap-1.5 mt-0.5">
                                    {booking.user?.email && (
                                        <a
                                            href={`mailto:${booking.user.email}?subject=Update regarding Order BKG-${booking.id}`}
                                            className="inline-flex items-center gap-1 text-[11px] font-bold text-cyan-300 hover:text-cyan-200 hover:underline"
                                            title="Email Client"
                                        >
                                            <Mail size={11} />
                                            <span>Email Client</span>
                                        </a>
                                    )}
                                    <span className="text-white/30 text-[10px]">|</span>
                                    <Link
                                        href={booking.user_id ? `/admin/messages?user_id=${booking.user_id}` : '/admin/messages'}
                                        className="inline-flex items-center gap-1 text-[11px] font-bold text-purple-300 hover:text-purple-200 hover:underline"
                                        title="Direct Live Chat with Client"
                                    >
                                        <MessageSquare size={11} />
                                        <span>Chat with Client</span>
                                    </Link>
                                </div>
                            </div>
                        </div>

                        {/* Target Website */}
                        <div className="flex items-center gap-3">
                            <div className="w-9 h-9 rounded-lg bg-white/10 border border-white/15 flex items-center justify-center text-blue-400 shrink-0">
                                <Globe size={18} />
                            </div>
                            <div className="min-w-0 flex-1">
                                <p className="text-[10px] text-slate-400 font-semibold">Target Website</p>
                                {websiteUrl ? (
                                    <a
                                        href={websiteUrl.startsWith('http') ? websiteUrl : `https://${websiteUrl}`}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="text-[12px] font-bold text-cyan-300 hover:text-cyan-200 hover:underline flex items-center gap-1 truncate"
                                    >
                                        <span className="truncate">{websiteUrl.replace(/^https?:\/\//, '')}</span>
                                        <ExternalLink size={12} className="shrink-0" />
                                    </a>
                                ) : (
                                    <span className="text-[12px] text-slate-400 italic">Not provided</span>
                                )}
                            </div>
                        </div>

                        {/* Keywords */}
                        <div className="flex items-center gap-3">
                            <div className="w-9 h-9 rounded-lg bg-white/10 border border-white/15 flex items-center justify-center text-amber-400 shrink-0">
                                <Tag size={18} />
                            </div>
                            <div className="min-w-0 flex-1">
                                <p className="text-[10px] text-slate-400 font-semibold">Target Keywords</p>
                                <p className="text-[12px] font-medium text-slate-200 truncate" title={targetKeywords || 'None'}>
                                    {targetKeywords || 'No keywords set'}
                                </p>
                            </div>
                        </div>

                        {/* Overall Progress Gauge */}
                        <div className="flex items-center gap-3 bg-white/5 p-2.5 rounded-lg border border-white/10">
                            <div className="flex-1 min-w-0">
                                <div className="flex justify-between items-center text-[11px] mb-1">
                                    <span className="text-slate-300 font-semibold">Total Progress</span>
                                    <span className="font-extrabold text-emerald-400">{overallProgress}%</span>
                                </div>
                                <div className="w-full h-1.5 bg-white/20 rounded-full overflow-hidden">
                                    <div
                                        className="h-full bg-gradient-to-r from-purple-400 to-emerald-400 rounded-full transition-all duration-500"
                                        style={{ width: `${overallProgress}%` }}
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Add Task Form (Ultra Compact Inline) */}
                {isAddingTask && (
                    <div className="bg-purple-50/60 rounded-xl border border-purple-200/80 p-3.5 shadow-2xs transition-all">
                        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-2.5">
                            <h3 className="text-[13px] font-bold text-purple-900 flex items-center gap-1.5">
                                <Plus size={15} className="text-purple-600" /> Create New Milestone Task
                            </h3>
                            <button onClick={() => setIsAddingTask(false)} className="text-slate-400 hover:text-slate-700">
                                <X size={15} />
                            </button>
                        </div>
                        <form onSubmit={handleAddTask} className="grid grid-cols-12 gap-2.5 items-end">
                            <div className="col-span-12 md:col-span-4">
                                <label className="block text-[11px] font-bold text-slate-700 mb-1">Task Title *</label>
                                <input
                                    type="text" required
                                    value={newTask.title}
                                    onChange={e => setNewTask('title', e.target.value)}
                                    className="w-full h-[34px] px-3 bg-white border border-slate-300 rounded-md text-[12px] focus:outline-none focus:border-purple-600"
                                    placeholder="e.g. Audit Delivery, Link Report"
                                />
                            </div>
                            <div className="col-span-12 md:col-span-4">
                                <label className="block text-[11px] font-bold text-slate-700 mb-1">Description / Links</label>
                                <input
                                    type="text"
                                    value={newTask.description}
                                    onChange={e => setNewTask('description', e.target.value)}
                                    className="w-full h-[34px] px-3 bg-white border border-slate-300 rounded-md text-[12px] focus:outline-none focus:border-purple-600"
                                    placeholder="e.g. Completed report https://..."
                                />
                            </div>
                            <div className="col-span-6 md:col-span-2">
                                <label className="block text-[11px] font-bold text-slate-700 mb-1">Initial %</label>
                                <input
                                    type="number" min="0" max="100" required
                                    value={newTask.progress}
                                    onChange={e => setNewTask('progress', e.target.value)}
                                    className="w-full h-[34px] px-3 bg-white border border-slate-300 rounded-md text-[12px] focus:outline-none focus:border-purple-600"
                                />
                            </div>
                            <div className="col-span-6 md:col-span-2 flex gap-1.5">
                                <button
                                    type="submit"
                                    disabled={adding}
                                    className="w-full h-[34px] bg-purple-600 hover:bg-purple-700 text-white text-[12px] font-bold rounded-md transition-all shadow-2xs disabled:opacity-50 flex items-center justify-center gap-1"
                                >
                                    <Save size={13} /> {adding ? 'Saving...' : 'Save'}
                                </button>
                            </div>
                        </form>
                    </div>
                )}

                {/* User & Role Management Style Clean Data Table */}
                <div className="bg-white rounded-md border border-slate-200/80 shadow-2xs overflow-hidden">
                    {/* Header Bar with Title & Search */}
                    <div className="px-5 py-4 border-b border-slate-200 bg-slate-50/50 flex flex-wrap items-center justify-between gap-4">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 rounded-md bg-[#0a66c2]/10 text-[#0a66c2] flex items-center justify-center border border-[#0a66c2]/20">
                                <FolderKanban size={20} />
                            </div>
                            <div>
                                <h2 className="text-base font-bold text-slate-900 leading-tight">Tasks & Work Deliverables</h2>
                                <p className="text-xs text-slate-600 mt-0.5">Manage project milestones, delivered links, and statuses</p>
                            </div>
                        </div>

                        <div className="flex items-center gap-2">
                            <span className="px-3 py-1 rounded-md bg-slate-100 text-slate-700 text-xs font-semibold border border-slate-200">
                                Total Tasks: <strong className="text-slate-900">{totalTasks}</strong>
                            </span>
                        </div>
                    </div>

                    {/* Table View */}
                    <div className="overflow-x-auto">
<div className="w-full overflow-x-auto overflow-y-hidden touch-pan-x border border-slate-200/80 rounded-xl shadow-2xs mb-4">
                        <table className="w-full min-w-[850px] text-left border-collapse">
                            <thead>
                                <tr className="border-b border-slate-200 bg-slate-50 text-xs font-bold text-slate-700">
                                    <th className="py-3 px-5 whitespace-nowrap">Task / Milestone</th>
                                    <th className="py-3 px-5 whitespace-nowrap">Description & Links</th>
                                    <th className="py-3 px-5 whitespace-nowrap">Status</th>
                                    <th className="py-3 px-5 text-right whitespace-nowrap">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 text-sm">
                                {totalTasks > 0 ? booking.tasks.map((task, idx) => (
                                    <tr key={task.id} className="hover:bg-slate-50/80 transition-colors group">
                                        {editingTaskId === task.id ? (
                                            <td colSpan="4" className="p-4 bg-slate-50/80">
                                                <form onSubmit={(e) => handleUpdateTask(e, task.id)} className="grid grid-cols-12 gap-3 items-center">
                                                    <div className="col-span-12 md:col-span-4">
                                                        <label className="block text-[10px] font-bold text-slate-500 mb-1">Task Title</label>
                                                        <input
                                                            type="text" required
                                                            value={editTaskData.title}
                                                            onChange={e => setEditTaskData('title', e.target.value)}
                                                            className="w-full h-[36px] px-3 bg-white border border-slate-300 rounded-md text-xs focus:outline-none focus:border-[#0a66c2]"
                                                        />
                                                    </div>
                                                    <div className="col-span-12 md:col-span-4">
                                                        <label className="block text-[10px] font-bold text-slate-500 mb-1">Description / Delivered Links</label>
                                                        <input
                                                            type="text"
                                                            value={editTaskData.description}
                                                            onChange={e => setEditTaskData('description', e.target.value)}
                                                            className="w-full h-[36px] px-3 bg-white border border-slate-300 rounded-md text-xs focus:outline-none focus:border-[#0a66c2]"
                                                            placeholder="e.g. Completed report https://..."
                                                        />
                                                    </div>
                                                    <div className="col-span-6 md:col-span-2">
                                                        <label className="block text-[10px] font-bold text-slate-500 mb-1">Status</label>
                                                        <div className="relative">
                                                            <select
                                                                style={{ WebkitAppearance: 'none', MozAppearance: 'none', appearance: 'none' }}
                                                                value={editTaskData.status}
                                                                onChange={e => setEditTaskData('status', e.target.value)}
                                                                className="w-full h-[36px] pl-3 pr-8 bg-white border border-slate-300 rounded-md text-xs focus:outline-none focus:border-[#0a66c2] font-bold cursor-pointer appearance-none"
                                                            >
                                                                <option value="pending">Pending</option>
                                                                <option value="ongoing">In Progress</option>
                                                                <option value="completed">Completed</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div className="col-span-6 md:col-span-2 flex items-center justify-end gap-1.5 pt-4">
                                                        <button
                                                            type="button" onClick={() => setEditingTaskId(null)}
                                                            className="px-3 py-1.5 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-md transition-colors"
                                                        >
                                                            Cancel
                                                        </button>
                                                        <button
                                                            type="submit" disabled={saving}
                                                            className="px-4 py-1.5 bg-[#0a66c2] text-white text-xs font-bold rounded-md hover:bg-[#084e96] transition-colors"
                                                        >
                                                            Save
                                                        </button>
                                                    </div>
                                                </form>
                                            </td>
                                        ) : (
                                            <>
                                                {/* Task / Milestone Column with Avatar Circle */}
                                                <td className="py-3.5 px-5 whitespace-nowrap">
                                                    <div className="flex items-center gap-3">
                                                        <div className="w-8 h-8 rounded-md bg-[#0a66c2]/10 text-[#0a66c2] font-bold text-xs flex items-center justify-center shrink-0 border border-[#0a66c2]/20">
                                                            #{idx + 1}
                                                        </div>
                                                        <div>
                                                            <p className="font-bold text-slate-900 text-sm group-hover:text-[#0a66c2] transition-colors">{task.title}</p>
                                                        </div>
                                                    </div>
                                                </td>

                                                {/* Description & Delivered Links Column */}
                                                <td className="py-3.5 px-5 whitespace-nowrap">
                                                    {task.description ? (
                                                        task.description.startsWith('http') ? (
                                                            <a
                                                                href={task.description}
                                                                target="_blank"
                                                                rel="noreferrer"
                                                                className="inline-flex items-center gap-1.5 font-semibold text-[#0a66c2] hover:underline bg-[#0a66c2]/10 text-xs px-2.5 py-1 rounded-md border border-[#0a66c2]/20"
                                                            >
                                                                <Link2 size={13} /> {task.description}
                                                            </a>
                                                        ) : (
                                                            <p className="text-slate-600 text-xs max-w-md">{task.description}</p>
                                                        )
                                                    ) : (
                                                        <span className="text-slate-400 italic text-xs">No notes added</span>
                                                    )}
                                                </td>

                                                {/* Status Column (Exact Dashboard Match) */}
                                                <td className="py-3.5 px-5 whitespace-nowrap">
                                                    <div className="relative inline-block">
                                                        <select
                                                            style={{ WebkitAppearance: 'none', MozAppearance: 'none', appearance: 'none' }}
                                                            value={task.status === 'completed' || Number(task.progress) === 100 ? 'completed' : (task.status === 'ongoing' || Number(task.progress) > 0 ? 'ongoing' : 'pending')}
                                                            onChange={(e) => {
                                                                const val = e.target.value;
                                                                const prog = val === 'completed' ? 100 : (val === 'ongoing' ? 50 : 0);
                                                                handleQuickProgressUpdate(task, prog);
                                                            }}
                                                            className={`appearance-none pl-3 pr-7 py-1 rounded-md text-xs font-bold cursor-pointer border transition-all outline-none ${
                                                                task.status === 'completed' || Number(task.progress) === 100
                                                                    ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                                                    : task.status === 'ongoing' || Number(task.progress) > 0
                                                                    ? 'bg-[#0a66c2]/10 text-[#0a66c2] border-[#0a66c2]/20'
                                                                    : 'bg-amber-50 text-amber-700 border-amber-200'
                                                            }`}
                                                        >
                                                            <option value="pending" className="bg-white text-slate-800 font-medium">Pending</option>
                                                            <option value="ongoing" className="bg-white text-slate-800 font-medium">In Progress</option>
                                                            <option value="completed" className="bg-white text-slate-800 font-medium">Completed</option>
                                                        </select>
                                                    
                                                    </div>
                                                </td>

                                                {/* Actions Column (Dashboard Match Style) */}
                                                <td className="py-3.5 px-5 text-right whitespace-nowrap">
                                                    <div className="flex items-center justify-end gap-1.5">
                                                        <button
                                                            onClick={() => startEditing(task)}
                                                            className="w-8 h-8 rounded-md border border-slate-200 text-slate-600 hover:text-[#0a66c2] hover:border-[#0a66c2]/30 hover:bg-[#0a66c2]/10 flex items-center justify-center transition-all"
                                                            title="Edit Task"
                                                        >
                                                            <Edit2 size={14} />
                                                        </button>
                                                        <button
                                                            onClick={() => handleDeleteTask(task.id)}
                                                            className="w-8 h-8 rounded-md border border-slate-200 text-rose-600 hover:text-white hover:bg-rose-600 hover:border-rose-600 flex items-center justify-center transition-all"
                                                            title="Delete Task"
                                                        >
                                                            <Trash2 size={14} />
                                                        </button>
                                                    </div>
                                                </td>
                                            </>
                                        )}
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan="4" className="py-12 text-center text-slate-400">
                                            <FolderKanban size={32} className="mx-auto text-slate-300 mb-2" />
                                            <p className="font-bold text-slate-700 text-[14px]">No tasks found</p>
                                            <p className="text-[12px]">Click "Add Task" to create a milestone for this booking.</p>
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
</div>
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}