import React, { useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import { ChevronLeft, FolderKanban, Plus, Trash2, Edit2, Save, X, Calendar, Home, CheckCircle, Globe, Tag, ExternalLink } from 'lucide-react';

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

    return (
        <AdminLayout>
            <Head title={`Booking BKG-${booking.id} Tasks`} />

            <div className="space-y-6 max-w-full mx-auto pb-20 font-inter">
                {/* Top Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <Link href="/admin/bookings" className="w-[34px] h-[34px] flex items-center justify-center rounded-[8px] bg-white border border-[#e3e4e8] text-[#727586] hover:bg-[#f4f0ff] hover:text-[#673ab7] hover:border-[#673ab7] transition-all">
                            <ChevronLeft size={20} />
                        </Link>
                        <h1 className="text-[24px] font-bold text-[#2f3344] tracking-tight">
                            Task Manager <span className="text-[#a0a3af] font-normal mx-2">|</span> BKG-{booking.id}
                        </h1>
                        <div className="flex items-center gap-2 text-[13px] text-[#727586] mt-1">
                            <Home size={16} className="text-[#727586]" />
                            <span className="text-[#c3c4ca]">-</span>
                            <span className="font-bold text-slate-800">{booking.user?.name}</span>
                        </div>
                    </div>
                    <button
                        onClick={() => setIsAddingTask(!isAddingTask)}
                        className={`px-6 py-2.5 rounded-[8px] text-[13px] font-bold transition-all flex items-center gap-2 shadow-sm ${isAddingTask
                                ? 'bg-white border border-[#e3e4e8] text-[#2f3344] hover:bg-[#fafbfc]'
                                : 'bg-[#673ab7] text-white hover:bg-[#5e35b1]'
                            }`}
                    >
                        {isAddingTask ? <X size={18} /> : <Plus size={18} />}
                        {isAddingTask ? 'Cancel' : 'Add New Task'}
                    </button>
                </div>

                {/* Info Card */}
                <div className="bg-white rounded-[12px] border border-[#e3e4e8] shadow-sm overflow-hidden p-6 space-y-6">
                    <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div className="flex items-start gap-3">
                            <div className="w-10 h-10 rounded-lg bg-[#f4f0ff] flex items-center justify-center text-[#673ab7]">
                                <FolderKanban size={20} />
                            </div>
                            <div>
                                <p className="text-[12px] text-[#727586] font-medium mb-0.5">Client</p>
                                <p className="text-[14px] font-bold text-[#2f3344]">{booking.user?.name}</p>
                            </div>
                        </div>
                        <div>
                            <p className="text-[12px] text-[#727586] font-medium mb-0.5">Plan / Service</p>
                            <p className="text-[14px] font-bold text-[#673ab7]">{booking.plan_name || booking.service?.title || 'Service Plan'}</p>
                        </div>
                        <div>
                            <p className="text-[12px] text-[#727586] font-medium mb-0.5">Booking Status</p>
                            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border capitalize mt-1
                                ${booking.status === 'active' || booking.status === 'ongoing' ? 'bg-[#f4f0ff] text-[#673ab7] border-[#e9e3ff]' :
                                    booking.status === 'completed' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-amber-50 text-amber-600 border-amber-100'}`}>
                                {booking.status || 'Active'}
                            </span>
                        </div>
                        <div>
                            <p className="text-[12px] text-[#727586] font-medium mb-0.5">Overall Service Progress</p>
                            <div className="flex items-center gap-3 mt-1">
                                <span className="text-[14px] font-bold text-[#2f3344]">
                                    {booking.tasks.length > 0 ? Math.round(booking.tasks.reduce((acc, t) => acc + Number(t.progress || 0), 0) / booking.tasks.length) : 0}%
                                </span>
                                <div className="w-28 h-2 bg-[#e3e4e8] rounded-full overflow-hidden">
                                    <div
                                        className="h-full bg-gradient-to-r from-[#AC6CFF] to-[#673ab7] rounded-full transition-all duration-500"
                                        style={{ width: `${booking.tasks.length > 0 ? Math.round(booking.tasks.reduce((acc, t) => acc + Number(t.progress || 0), 0) / booking.tasks.length) : 0}%` }}
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Client Submitted Target Website & Keywords Banner */}
                    <div className="pt-5 border-t border-[#e3e4e8] grid grid-cols-1 md:grid-cols-2 gap-6 bg-[#faf8ff] p-4 rounded-lg border border-[#e9e3ff]">
                        <div className="space-y-1">
                            <div className="flex items-center gap-2 text-[#673ab7]">
                                <Globe size={16} />
                                <span className="text-[12px] font-bold uppercase tracking-wider">Target Website URL</span>
                            </div>
                            {websiteUrl ? (
                                <a
                                    href={websiteUrl.startsWith('http') ? websiteUrl : `https://${websiteUrl}`}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="text-[14px] font-semibold text-[#0a66c2] hover:underline flex items-center gap-1.5 break-all mt-1"
                                >
                                    {websiteUrl}
                                    <ExternalLink size={14} className="shrink-0" />
                                </a>
                            ) : (
                                <p className="text-[13px] text-slate-400 italic">No URL provided</p>
                            )}
                        </div>

                        <div className="space-y-1">
                            <div className="flex items-center gap-2 text-[#673ab7]">
                                <Tag size={16} />
                                <span className="text-[12px] font-bold uppercase tracking-wider">Target Keywords</span>
                            </div>
                            {targetKeywords ? (
                                <p className="text-[13px] font-medium text-slate-800 whitespace-pre-line leading-relaxed bg-white p-2.5 rounded border border-[#e3e4e8] mt-1">
                                    {targetKeywords}
                                </p>
                            ) : (
                                <p className="text-[13px] text-slate-400 italic">No target keywords provided</p>
                            )}
                        </div>
                    </div>
                </div>

                {/* Add Task Form */}
                {isAddingTask && (
                    <div className="bg-[#f4f0ff]/50 rounded-[12px] border border-[#e9e3ff] shadow-sm p-6">
                        <h3 className="text-[15px] font-bold text-[#673ab7] mb-4">Create New Task</h3>
                        <form onSubmit={handleAddTask} className="space-y-4">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label className="block text-[13px] font-bold text-[#2f3344] mb-1.5">Task Title</label>
                                    <input
                                        type="text"
                                        required
                                        value={newTask.title}
                                        onChange={e => setNewTask('title', e.target.value)}
                                        className="w-full h-[42px] px-4 bg-white border border-[#e3e4e8] rounded-[8px] text-[14px] focus:outline-none focus:border-[#673ab7] focus:ring-1 focus:ring-[#673ab7]"
                                        placeholder="e.g. Technical Audit"
                                    />
                                </div>
                                <div className="grid grid-cols-2 gap-5">
                                    <div>
                                        <label className="block text-[13px] font-bold text-[#2f3344] mb-1.5">Progress (%)</label>
                                        <input
                                            type="number"
                                            min="0" max="100" required
                                            value={newTask.progress}
                                            onChange={e => setNewTask('progress', e.target.value)}
                                            className="w-full h-[42px] px-4 bg-white border border-[#e3e4e8] rounded-[8px] text-[14px] focus:outline-none focus:border-[#673ab7] focus:ring-1 focus:ring-[#673ab7]"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-[13px] font-bold text-[#2f3344] mb-1.5">Status</label>
                                        <select
                                            value={newTask.status}
                                            onChange={e => setNewTask('status', e.target.value)}
                                            className="w-full h-[42px] px-4 bg-white border border-[#e3e4e8] rounded-[8px] text-[14px] focus:outline-none focus:border-[#673ab7] focus:ring-1 focus:ring-[#673ab7]"
                                        >
                                            <option value="pending">Pending</option>
                                            <option value="ongoing">Ongoing</option>
                                            <option value="completed">Completed</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label className="block text-[13px] font-bold text-[#2f3344] mb-1.5">Description</label>
                                <textarea
                                    rows="2"
                                    value={newTask.description}
                                    onChange={e => setNewTask('description', e.target.value)}
                                    className="w-full px-4 py-3 bg-white border border-[#e3e4e8] rounded-[8px] text-[14px] focus:outline-none focus:border-[#673ab7] focus:ring-1 focus:ring-[#673ab7]"
                                    placeholder="Task details..."
                                ></textarea>
                            </div>
                            <div className="flex justify-end pt-2">
                                <button
                                    type="submit"
                                    disabled={adding}
                                    className="px-6 py-2.5 bg-[#673ab7] hover:bg-[#5e35b1] text-white text-[13px] font-bold rounded-[8px] transition-all disabled:opacity-50 shadow-sm"
                                >
                                    {adding ? 'Saving...' : 'Save Task'}
                                </button>
                            </div>
                        </form>
                    </div>
                )}

                {/* Tasks List Card */}
                <div className="bg-white rounded-[12px] border border-[#e3e4e8] shadow-sm overflow-hidden">
                    <div className="px-7 py-5 border-b border-[#e3e4e8] bg-[#fafbfc] flex items-center justify-between">
                        <h2 className="text-[15px] font-bold text-[#2f3344]">Project Roadmap Tasks</h2>
                        <span className="text-xs text-slate-500 font-medium">1-Click Progress Controller</span>
                    </div>

                    <div className="divide-y divide-[#f1f2f4]">
                        {booking.tasks.length > 0 ? booking.tasks.map((task) => (
                            <div key={task.id} className="px-6 py-4 hover:bg-[#fafbfc] transition-colors group">
                                {editingTaskId === task.id ? (
                                    <form onSubmit={(e) => handleUpdateTask(e, task.id)} className="space-y-3">
                                        <div className="grid grid-cols-12 gap-3 items-center">
                                            <div className="col-span-12 md:col-span-4">
                                                <input
                                                    type="text" required
                                                    value={editTaskData.title}
                                                    onChange={e => setEditTaskData('title', e.target.value)}
                                                    className="w-full h-[36px] px-3 border border-[#e3e4e8] rounded-[6px] text-[13px] focus:outline-none focus:border-[#673ab7]"
                                                    placeholder="Task Title"
                                                />
                                            </div>
                                            <div className="col-span-12 md:col-span-3">
                                                <input
                                                    type="text"
                                                    value={editTaskData.description}
                                                    onChange={e => setEditTaskData('description', e.target.value)}
                                                    className="w-full h-[36px] px-3 border border-[#e3e4e8] rounded-[6px] text-[13px] focus:outline-none focus:border-[#673ab7]"
                                                    placeholder="Description"
                                                />
                                            </div>
                                            <div className="col-span-6 md:col-span-2">
                                                <input
                                                    type="number" min="0" max="100" required
                                                    value={editTaskData.progress}
                                                    onChange={e => setEditTaskData('progress', e.target.value)}
                                                    className="w-full h-[36px] px-3 border border-[#e3e4e8] rounded-[6px] text-[13px] focus:outline-none focus:border-[#673ab7]"
                                                    placeholder="Progress %"
                                                />
                                            </div>
                                            <div className="col-span-6 md:col-span-2">
                                                <select
                                                    value={editTaskData.status}
                                                    onChange={e => setEditTaskData('status', e.target.value)}
                                                    className="w-full h-[36px] px-2 border border-[#e3e4e8] rounded-[6px] text-[13px] focus:outline-none focus:border-[#673ab7]"
                                                >
                                                    <option value="pending">Pending</option>
                                                    <option value="ongoing">Ongoing</option>
                                                    <option value="completed">Completed</option>
                                                </select>
                                            </div>
                                            <div className="col-span-12 md:col-span-1 flex justify-end gap-1">
                                                <button
                                                    type="button"
                                                    onClick={() => setEditingTaskId(null)}
                                                    className="p-2 text-[#727586] hover:bg-[#f1f2f4] rounded-[6px] transition-colors"
                                                    title="Cancel"
                                                >
                                                    <X size={16} />
                                                </button>
                                                <button
                                                    type="submit"
                                                    disabled={saving}
                                                    className="p-2 bg-[#673ab7] hover:bg-[#5e35b1] text-white rounded-[6px] transition-colors shadow-sm disabled:opacity-50"
                                                    title="Save"
                                                >
                                                    <Save size={16} />
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                ) : (
                                    <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                        <div className="flex-1 min-w-0">
                                            <div className="flex items-center gap-2.5 mb-1">
                                                <h4 className="text-[14px] font-bold text-[#2f3344] group-hover:text-[#673ab7] transition-colors">{task.title}</h4>
                                                <span className={`shrink-0 inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider
                                                    ${task.status === 'ongoing' ? 'bg-[#f4f0ff] text-[#673ab7] border border-[#e9e3ff]' :
                                                        task.status === 'completed' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-[#f8f9fa] text-[#727586] border border-[#e3e4e8]'}`}>
                                                    {task.status}
                                                </span>
                                            </div>
                                            {task.description && (
                                                <p className="text-[12px] text-[#727586]">{task.description}</p>
                                            )}
                                        </div>

                                        {/* Progress Controls & Presets */}
                                        <div className="flex flex-wrap items-center gap-4 shrink-0">
                                            {/* Quick Preset Buttons */}
                                            <div className="flex items-center gap-1 bg-[#f4f5f8] p-1 rounded-lg border border-[#e3e4e8]">
                                                <button
                                                    onClick={() => handleQuickProgressUpdate(task, 0)}
                                                    className={`px-2 py-1 text-[11px] font-bold rounded ${Number(task.progress) === 0 ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500 hover:text-slate-900'}`}
                                                >
                                                    0%
                                                </button>
                                                <button
                                                    onClick={() => handleQuickProgressUpdate(task, 25)}
                                                    className={`px-2 py-1 text-[11px] font-bold rounded ${Number(task.progress) === 25 ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500 hover:text-slate-900'}`}
                                                >
                                                    25%
                                                </button>
                                                <button
                                                    onClick={() => handleQuickProgressUpdate(task, 50)}
                                                    className={`px-2 py-1 text-[11px] font-bold rounded ${Number(task.progress) === 50 ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500 hover:text-slate-900'}`}
                                                >
                                                    50%
                                                </button>
                                                <button
                                                    onClick={() => handleQuickProgressUpdate(task, 75)}
                                                    className={`px-2 py-1 text-[11px] font-bold rounded ${Number(task.progress) === 75 ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-500 hover:text-slate-900'}`}
                                                >
                                                    75%
                                                </button>
                                                <button
                                                    onClick={() => handleQuickProgressUpdate(task, 100)}
                                                    className={`px-2.5 py-1 text-[11px] font-bold rounded flex items-center gap-1 ${Number(task.progress) === 100 ? 'bg-emerald-500 text-white shadow-2xs' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'}`}
                                                >
                                                    <CheckCircle size={12} /> 100%
                                                </button>
                                            </div>

                                            {/* Progress Bar Display */}
                                            <div className="flex items-center gap-2 w-[110px]">
                                                <div className="flex-1 h-2 bg-[#e3e4e8] rounded-full overflow-hidden">
                                                    <div
                                                        className={`h-full rounded-full transition-all duration-300 ${task.progress >= 100 ? 'bg-emerald-500' : 'bg-[#673ab7]'}`}
                                                        style={{ width: `${task.progress}%` }}
                                                    />
                                                </div>
                                                <span className={`text-[12px] font-bold w-8 text-right ${task.progress >= 100 ? 'text-emerald-600' : 'text-[#2f3344]'}`}>
                                                    {task.progress}%
                                                </span>
                                            </div>

                                            {/* Actions */}
                                            <div className="flex items-center gap-1.5 border-l border-[#e3e4e8] pl-3">
                                                <button
                                                    onClick={() => startEditing(task)}
                                                    className="w-[30px] h-[30px] flex items-center justify-center rounded-[6px] text-[#fbbf24] bg-[#fffbeb] hover:bg-[#fbbf24] hover:text-white transition-all shadow-2xs border border-amber-200"
                                                    title="Edit Task Details"
                                                >
                                                    <Edit2 size={14} />
                                                </button>
                                                <button
                                                    onClick={() => handleDeleteTask(task.id)}
                                                    className="w-[30px] h-[30px] flex items-center justify-center rounded-[6px] text-[#ef4444] bg-[#fee2e2]/50 hover:bg-[#ef4444] hover:text-white transition-all shadow-2xs border border-rose-200"
                                                    title="Delete Task"
                                                >
                                                    <Trash2 size={14} />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                )}
                            </div>
                        )) : (
                            <div className="px-7 py-20 text-center">
                                <div className="flex flex-col items-center gap-3 text-[#727586]">
                                    <div className="w-16 h-16 bg-[#f8f9fa] rounded-full flex items-center justify-center mb-2">
                                        <FolderKanban size={30} className="text-[#c3c4ca]" />
                                    </div>
                                    <p className="text-[16px] font-bold text-[#2f3344]">No tasks found</p>
                                    <p className="text-[14px]">Create the first task for this client's booking.</p>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
