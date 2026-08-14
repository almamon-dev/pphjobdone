export default function PrimaryButton({
    className = '',
    disabled,
    children,
    ...props
}) {
    return (
        <button
            {...props}
            className={
                `inline-flex items-center justify-center rounded-md border border-transparent bg-[#0a66c2] px-4 py-2 text-xs font-semibold text-white shadow-2xs transition-all hover:bg-[#084e96] focus:bg-[#084e96] focus:outline-none focus:ring-2 focus:ring-[#0a66c2]/20 active:bg-[#063b72] disabled:opacity-50 cursor-pointer ${
                    disabled && 'opacity-50 cursor-not-allowed'
                } ` + className
            }
            disabled={disabled}
        >
            {children}
        </button>
    );
}
