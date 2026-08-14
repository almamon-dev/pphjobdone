import React from 'react';
import AdminLayout from '@/Layouts/AdminLayout';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, usePage } from '@inertiajs/react';
import UpdatePasswordForm from './Partials/UpdatePasswordForm';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm';
import { User, Shield } from 'lucide-react';

export default function Edit({ mustVerifyEmail, status }) {
    const { props } = usePage();
    const isAdmin = Boolean(props.auth?.user?.is_admin);

    const mainContent = (
        <div className="space-y-4 max-w-[1600px] mx-auto pb-12">
            {/* COMPACT TOP HEADER */}
            <div className="bg-white rounded-md p-4 border border-slate-200/80 shadow-2xs flex items-center justify-between">
                <div className="flex items-center gap-2">
                    <User size={20} className="text-[#0a66c2]" />
                    <div>
                        <h1 className="text-lg font-bold text-slate-800 leading-tight">
                            Account Profile & Security
                        </h1>
                        <p className="text-xs text-slate-500 mt-0.5">
                            Update your admin account credentials, email, and password settings.
                        </p>
                    </div>
                </div>

                {isAdmin && (
                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-purple-50 text-purple-700 text-xs font-bold border border-purple-200">
                        <Shield size={14} /> Administrator
                    </span>
                )}
            </div>

            {/* FULL 2-COLUMN GRID LAYOUT */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div className="bg-white rounded-md border border-slate-200/80 shadow-2xs p-5">
                    <UpdateProfileInformationForm
                        mustVerifyEmail={mustVerifyEmail}
                        status={status}
                        className="w-full"
                    />
                </div>

                <div className="bg-white rounded-md border border-slate-200/80 shadow-2xs p-5">
                    <UpdatePasswordForm className="w-full" />
                </div>
            </div>
        </div>
    );

    if (isAdmin) {
        return (
            <AdminLayout>
                <Head title="Profile Settings" />
                {mainContent}
            </AdminLayout>
        );
    }

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Profile
                </h2>
            }
        >
            <Head title="Profile" />
            <div className="py-12">
                {mainContent}
            </div>
        </AuthenticatedLayout>
    );
}
