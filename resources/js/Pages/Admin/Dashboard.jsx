import AdminLayout from "@/Layouts/AdminLayout";
import { Head } from "@inertiajs/react";
import {
    Users,
    Briefcase,
    BarChart,
} from "lucide-react";
import React from "react";

export default function Dashboard({ auth, stats }) {
    const user = auth.user;

    return (
        <AdminLayout>
            <Head title="Dashboard" />

            <div className="space-y-8">
                <div>
                    <h1 className="text-2xl font-bold text-gray-800">Overview</h1>
                    <p className="text-sm text-gray-500">Welcome back, {user.name}.</p>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div className="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                        <div className="flex items-center justify-between mb-2">
                            <span className="text-sm text-gray-500 uppercase font-semibold">Users</span>
                            <Users className="text-blue-500" size={20} />
                        </div>
                        <div className="text-3xl font-bold">{stats.users}</div>
                    </div>
                    <div className="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                        <div className="flex items-center justify-between mb-2">
                            <span className="text-sm text-gray-500 uppercase font-semibold">Services</span>
                            <Briefcase className="text-purple-500" size={20} />
                        </div>
                        <div className="text-3xl font-bold">{stats.services}</div>
                    </div>
                    <div className="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                        <div className="flex items-center justify-between mb-2">
                            <span className="text-sm text-gray-500 uppercase font-semibold">Bookings</span>
                            <BarChart className="text-emerald-500" size={20} />
                        </div>
                        <div className="text-3xl font-bold">{stats.bookings}</div>
                    </div>
                </div>

                <div className="bg-white p-10 rounded-lg border border-gray-200 shadow-sm min-h-[300px] flex items-center justify-center">
                    <div className="text-center">
                        <h2 className="text-xl font-semibold mb-2">System Activity</h2>
                        <p className="text-gray-500">Welcome to the management dashboard. Use the sidebar to naviage.</p>
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
