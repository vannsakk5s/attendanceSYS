<div class="mt-2">
    <div class="relative">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-slate-200"></div>
        </div>
        <div class="relative flex justify-center text-xs uppercase">
            <span class="bg-white px-3 text-slate-500 font-semibold tracking-wider">Or continue with</span>
        </div>
    </div>

    <!-- 3 Social Auth Boxes (Google, GitHub, Telegram) -->
    <div class="items-center justify-center mt-4 flex flex-wrap gap-3">
        <!-- 1. Google Box -->
        <a href="{{ route('social.redirect', ['provider' => 'google']) }}"
           title="Sign in with Google"
           class="flex flex-col sm:flex-row items-center justify-center gap-1.5 py-2.5 px-3 border border-slate-200 rounded-xl hover:bg-slate-50 hover:border-slate-300 hover:shadow-sm transition-all duration-150 bg-white group cursor-pointer text-center">
            <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24">
                <path fill="#EA4335" d="M12 5c1.6 0 3 .6 4.1 1.7l3.1-3.1C17.3 1.8 14.8 1 12 1 7.4 1 3.5 3.6 1.6 7.4l3.7 2.9C6.2 7.3 8.9 5 12 5z"/>
                <path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.6-.2-2.3H12v4.6h6.5c-.3 1.5-1.1 2.8-2.4 3.7l3.7 2.9c2.2-2 3.7-5 3.7-8.9z"/>
                <path fill="#FBBC05" d="M5.3 14.7c-.2-.7-.4-1.5-.4-2.7 0-1.1.2-1.9.4-2.7L1.6 6.4C.6 8.4 0 10.6 0 13s.6 4.6 1.6 6.6l3.7-2.9z"/>
                <path fill="#34A853" d="M12 23c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3.1 0-5.8-2.3-6.7-5.3L1.6 15.9C3.5 19.8 7.4 23 12 23z"/>
            </svg>
            <span class="text-xs font-semibold text-slate-700 group-hover:text-slate-900">Google</span>
        </a>

        <!-- 2. GitHub Box -->
        <a href="{{ route('social.redirect', ['provider' => 'github']) }}"
           title="Sign in with GitHub"
           class="flex flex-col sm:flex-row items-center justify-center gap-1.5 py-2.5 px-3 border border-slate-200 rounded-xl hover:bg-slate-50 hover:border-slate-300 hover:shadow-sm transition-all duration-150 bg-white group cursor-pointer text-center">
            <svg class="w-5 h-5 flex-shrink-0 text-slate-900" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
            </svg>
            <span class="text-xs font-semibold text-slate-700 group-hover:text-slate-900">GitHub</span>
        </a>

        <!-- 3. Telegram Box -->
        @if (config('services.telegram.bot_username'))
            <button type="button"
                    onclick="openTelegramLogin()"
                    title="Sign in with Telegram"
                    class="flex flex-col sm:flex-row items-center justify-center gap-1.5 py-2.5 px-3 border border-slate-200 rounded-xl hover:bg-sky-50 hover:border-sky-300 hover:shadow-sm transition-all duration-150 bg-white group cursor-pointer text-center">
                <svg class="w-5 h-5 flex-shrink-0 text-sky-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .37z"/>
                </svg>
                <span class="text-xs font-semibold text-sky-600 group-hover:text-sky-700">Telegram</span>
            </button>
            <script async src="https://telegram.org/js/telegram-widget.js?22"></script>
            <script>
                function openTelegramLogin() {
                    Telegram.Login.auth({ bot_id: '{{ explode(":", config("services.telegram.bot_token"))[0] ?? "" }}', request_access: true }, (data) => {
                        if (!data) {
                            alert('Telegram login cancelled or failed.');
                            return;
                        }
                        const params = new URLSearchParams(data);
                        window.location.href = "{{ route('social.telegram.callback') }}?" + params.toString();
                    });
                }
            </script>
        @else
            <a href="javascript:void(0)"
               onclick="alert('Telegram Bot is not configured yet. Please add TELEGRAM_BOT_USERNAME and TELEGRAM_BOT_TOKEN in your .env file.')"
               title="Sign in with Telegram"
               class="flex flex-col sm:flex-row items-center justify-center gap-1.5 py-2.5 px-3 border border-slate-200 rounded-xl hover:bg-sky-50 hover:border-sky-300 hover:shadow-sm transition-all duration-150 bg-white group cursor-pointer text-center">
                <svg class="w-5 h-5 flex-shrink-0 text-sky-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .37z"/>
                </svg>
                <span class="text-xs font-semibold text-slate-700 group-hover:text-slate-900">Telegram</span>
            </a>
        @endif
    </div>
</div>
