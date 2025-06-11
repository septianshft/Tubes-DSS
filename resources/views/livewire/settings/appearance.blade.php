<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<script>
    // Ensure setDarkClass function is available globally
    if (typeof window.setDarkClass === 'undefined') {
        window.setDarkClass = () => {
            const isDark = localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)

            // Use requestAnimationFrame to ensure DOM is ready
            requestAnimationFrame(() => {
                if (document.documentElement) {
                    if (isDark) {
                        document.documentElement.classList.add('dark')
                    } else {
                        document.documentElement.classList.remove('dark')
                    }
                }
            })
        }
    }
</script>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Appearance')" :subheading=" __('Update the appearance settings for your account')">
        <div x-data="{
            theme: localStorage.theme || '',
            init() {
                // Set initial theme immediately
                this.setTheme(this.theme || 'system')

                this.$watch('theme', (value) => {
                    this.setTheme(value)
                })
            },
            setTheme(value) {
                // Use nextTick to ensure DOM is ready and avoid conflicts
                this.$nextTick(() => {
                    if (value === 'light') {
                        this.lightMode()
                    } else if (value === 'dark') {
                        this.darkMode()
                    } else {
                        this.systemMode()
                    }
                })
            },
            darkMode() {
                this.theme = 'dark'
                localStorage.theme = 'dark'
                this.safeSetDarkClass()
            },
            lightMode() {
                this.theme = 'light'
                localStorage.theme = 'light'
                this.safeSetDarkClass()
            },
            systemMode() {
                this.theme = 'system'
                localStorage.removeItem('theme')
                this.safeSetDarkClass()
            },
            safeSetDarkClass() {
                // Ensure DOM is stable before class manipulation
                if (typeof window.setDarkClass === 'function') {
                    requestAnimationFrame(() => {
                        window.setDarkClass()
                    })
                }
            }
        }">
            <flux:radio.group variant="segmented" x-model="theme">
                <flux:radio value="light" icon="sun">{{ __('Light') }}</flux:radio>
                <flux:radio value="dark" icon="moon">{{ __('Dark') }}</flux:radio>
                <flux:radio value="system" icon="computer-desktop">{{ __('System') }}</flux:radio>
            </flux:radio.group>
        </div>
    </x-settings.layout>
</section>
