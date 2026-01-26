<x-app-layout>
    <div class="min-h-screen bg-gradient-to-b from-sky-50 to-white">
        <div class="max-w-3xl mx-auto px-4 py-16">

            <!-- Success Header -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-500 mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    {{ __('subscription.welcome_title') }}
                </h1>

                <p class="text-xl text-gray-600 mb-3">
                    {{ __('subscription.welcome_message') }}
                </p>

                <div class="inline-flex items-center gap-2 bg-green-100 text-green-700 px-4 py-2 rounded-full text-sm font-medium">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ __('subscription.trial_info') }}
                </div>
            </div>

            <!-- Setup Steps Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
                <div class="bg-sky-500 px-8 py-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">{{ __('subscription.quick_setup') }}</h2>
                            <p class="text-sky-100 text-sm">{{ __('subscription.setup_time') }}</p>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-gray-100">
                    <!-- Step 1 -->
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-5 px-8 py-5 hover:bg-sky-50 transition-colors group">
                        <div class="w-12 h-12 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center text-lg font-bold group-hover:bg-sky-500 group-hover:text-white transition-colors">
                            1
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 text-lg">{{ __('subscription.step_1_title') }}</h3>
                            <p class="text-gray-500">{{ __('subscription.step_1') }}</p>
                        </div>
                        <svg class="w-6 h-6 text-gray-400 group-hover:text-sky-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>

                    <!-- Step 2 -->
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-5 px-8 py-5 hover:bg-sky-50 transition-colors group">
                        <div class="w-12 h-12 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center text-lg font-bold group-hover:bg-sky-500 group-hover:text-white transition-colors">
                            2
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 text-lg">{{ __('subscription.step_2_title') }}</h3>
                            <p class="text-gray-500">{{ __('subscription.step_2') }}</p>
                        </div>
                        <svg class="w-6 h-6 text-gray-400 group-hover:text-sky-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>

                    <!-- Step 3 -->
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-5 px-8 py-5 hover:bg-sky-50 transition-colors group">
                        <div class="w-12 h-12 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center text-lg font-bold group-hover:bg-sky-500 group-hover:text-white transition-colors">
                            3
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 text-lg">{{ __('subscription.step_3_title') }}</h3>
                            <p class="text-gray-500">{{ __('subscription.step_3') }}</p>
                        </div>
                        <svg class="w-6 h-6 text-gray-400 group-hover:text-sky-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-10">
                <a href="{{ route('settings.index') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-sky-500 text-white font-semibold rounded-xl hover:bg-sky-600 transition-colors text-lg">
                    {{ __('subscription.start_setup') }}
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>

                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-gray-700 font-semibold rounded-xl border-2 border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition-colors text-lg">
                    {{ __('subscription.go_to_dashboard') }}
                </a>
            </div>

            <!-- Help -->
            <p class="text-center text-gray-500">
                {{ __('subscription.need_help') }}
                <a href="mailto:contactus@recaller.io" class="text-sky-500 hover:text-sky-600 font-medium">contactus@recaller.io</a>
            </p>

        </div>
    </div>
</x-app-layout>
