<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
	<!-- Primary Navigation Menu -->
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="flex justify-between h-16">
			<div class="flex">
				<!-- Logo -->
				<div class="shrink-0 flex items-center">
					<a href="{{ route('dashboard') }}">
						<x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
					</a>
				</div>
				
				<!-- Navigation Links -->
				<div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
					<x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
						{{ __('khairat.dashboard') }}
					</x-nav-link>
				</div>
				
				<!-- Member Section -->
				@if(Auth::user()->is_admin)
					<div class="hidden sm:flex sm:items-center sm:ms-6">
						<x-dropdown align="left" width="48">
							<x-slot name="trigger">
								<button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
									<div>{{ __('khairat.admin') }}</div>
									
									<div class="ms-1">
										<svg class="fill-current h-4 w-4"
												xmlns="http://www.w3.org/2000/svg"
												viewBox="0 0 20 20">
											<path fill-rule="evenodd"
													d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
													clip-rule="evenodd" />
										</svg>
									</div>
								</button>
							</x-slot>
							
							<x-slot name="content">
								<x-dropdown-link :href="route('admin.dashboard')">
									<div class="flex">
										<svg xmlns="http://www.w3.org/2000/svg"
												class="w-5 h-5 mr-2"
												fill="none"
												viewBox="0 0 24 24"
												stroke="currentColor">
											<path stroke-linecap="round"
													stroke-linejoin="round"
													stroke-width="2"
													d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
										</svg>
										
										{{ __('khairat.dashboard') }}
									</div>
								</x-dropdown-link>
								
								<x-dropdown-link :href="route('admin.payment-verification')">
									<div class="flex">
										<svg xmlns="http://www.w3.org/2000/svg"
												class="w-5 h-5 mr-2"
												fill="none"
												viewBox="0 0 24 24"
												stroke="currentColor">
											<path stroke-linecap="round"
													stroke-linejoin="round"
													stroke-width="2"
													d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
										</svg>
										
										{{ __('khairat.payment_verification') }}
									</div>
								</x-dropdown-link>
								
								<x-dropdown-link :href="route('admin.members')">
									<div class="flex">
										<svg xmlns="http://www.w3.org/2000/svg"
												class="w-5 h-5 mr-2"
												fill="none"
												viewBox="0 0 24 24"
												stroke="currentColor">
											<path stroke-linecap="round"
													stroke-linejoin="round"
													stroke-width="2"
													d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
										</svg>
										
										{{ __('khairat.members') }}
									</div>
								</x-dropdown-link>
								
								<x-dropdown-link :href="route('admin.reports')">
									<div class="flex">
										<svg xmlns="http://www.w3.org/2000/svg"
												class="w-5 h-5 mr-2"
												fill="none"
												viewBox="0 0 24 24"
												stroke="currentColor">
											<path stroke-linecap="round"
													stroke-linejoin="round"
													stroke-width="2"
													d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
										</svg>
										
										{{ __('khairat.reports') }}
									</div>
								</x-dropdown-link>
								
								<x-dropdown-link :href="route('admin.settings')">
									<div class="flex">
										<svg xmlns="http://www.w3.org/2000/svg"
												class="w-5 h-5 mr-2"
												fill="none"
												viewBox="0 0 24 24"
												stroke="currentColor">
											<path stroke-linecap="round"
													stroke-linejoin="round"
													stroke-width="2"
													d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
											<path stroke-linecap="round"
													stroke-linejoin="round"
													stroke-width="2"
													d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
										</svg>
										
										{{ __('khairat.settings') }}
									</div>
								</x-dropdown-link>
							</x-slot>
						</x-dropdown>
					</div>
				@endif
				
				<!-- Member Section -->
				<div class="hidden sm:flex sm:items-center sm:ms-6">
					<x-dropdown align="left" width="48">
						<x-slot name="trigger">
							<button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
								<div>{{ __('khairat.membership') }}</div>
								
								<div class="ms-1">
									<svg class="fill-current h-4 w-4"
											xmlns="http://www.w3.org/2000/svg"
											viewBox="0 0 20 20">
										<path fill-rule="evenodd"
												d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
												clip-rule="evenodd" />
									</svg>
								</div>
							</button>
						</x-slot>
						
						<x-slot name="content">
							<x-dropdown-link :href="route('member.show')">
								<div class="flex">
									<svg xmlns="http://www.w3.org/2000/svg"
											class="w-5 h-5 mr-2"
											fill="none"
											viewBox="0 0 24 24"
											stroke="currentColor">
										<path stroke-linecap="round"
												stroke-linejoin="round"
												stroke-width="2"
												d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
									</svg>
									
									{{ __('khairat.my_profile') }}
								</div>
							</x-dropdown-link>
							
							<x-dropdown-link :href="route('dependent.index')">
								<div class="flex">
									<svg xmlns="http://www.w3.org/2000/svg"
											class="w-5 h-5 mr-2"
											fill="none"
											viewBox="0 0 24 24"
											stroke="currentColor">
										<path stroke-linecap="round"
												stroke-linejoin="round"
												stroke-width="2"
												d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
									</svg>
									
									{{ __('khairat.family_members') }}
								</div>
							</x-dropdown-link>
							
							<x-dropdown-link :href="route('payments.create')">
								<div class="flex">
									<svg xmlns="http://www.w3.org/2000/svg"
											class="w-5 h-5 mr-2"
											fill="none"
											viewBox="0 0 24 24"
											stroke="currentColor">
										<path stroke-linecap="round"
												stroke-linejoin="round"
												stroke-width="2"
												d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
									</svg>
									
									{{ __('khairat.yuran') }}
								</div>
							</x-dropdown-link>
							
							<x-dropdown-link :href="route('payments.index')">
								<div class="flex">
									<svg xmlns="http://www.w3.org/2000/svg"
											class="w-5 h-5 mr-2"
											fill="none"
											viewBox="0 0 24 24"
											stroke="currentColor">
										<path stroke-linecap="round"
												stroke-linejoin="round"
												stroke-width="2"
												d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
									</svg>
									
									{{ __('khairat.sejarah_pembayaran') }}
								</div>
							</x-dropdown-link>
							
							{{--                            <x-dropdown-link :href="route('messages.index', [], false)">--}}
							{{--                                <div class="flex">--}}
							{{--                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">--}}
							{{--                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />--}}
							{{--                                    </svg>--}}
							{{--                                    --}}
							{{--                                    {{ __('Mesej') }}--}}
							{{--                                </div>--}}
							{{--                            </x-dropdown-link>--}}
						</x-slot>
					</x-dropdown>
				</div>
			</div>
			
			<!-- Settings Dropdown -->
			<div class="hidden sm:flex sm:items-center sm:ms-6">
				<!-- Notification Indicator -->
				<div class="mr-3">
					<livewire:notification-indicator />
				</div>
				
				<!-- Language Switcher -->
				<div class="mr-3">
					<livewire:components.language-switcher />
				</div>

				<x-dropdown align="right" width="48">
					<x-slot name="trigger">
						<button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
							<div>{{ Auth::user()->name }}</div>
							
							<div class="ms-1">
								<svg class="fill-current h-4 w-4"
										xmlns="http://www.w3.org/2000/svg"
										viewBox="0 0 20 20">
									<path fill-rule="evenodd"
											d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
											clip-rule="evenodd" />
								</svg>
							</div>
						</button>
					</x-slot>
					
					<x-slot name="content">
						<x-dropdown-link :href="route('profile.edit')">
							{{ __('khairat.profile') }}
						</x-dropdown-link>
						
						<x-dropdown-link :href="route('notifications.preferences')">
							{{ __('khairat.notification_preferences') }}
						</x-dropdown-link>
						
						<!-- Authentication -->
						<form method="POST" action="{{ route('logout') }}">
							@csrf
							
							<x-dropdown-link :href="route('logout')"
									onclick="event.preventDefault();
                                                this.closest('form').submit();">
								{{ __('khairat.logout') }}
							</x-dropdown-link>
						</form>
					</x-slot>
				</x-dropdown>
			</div>
			
			<!-- Hamburger -->
			<div class="-me-2 flex items-center sm:hidden">
				<button @click="open = ! open"
						class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
					<svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
						<path :class="{'hidden': open, 'inline-flex': ! open }"
								class="inline-flex"
								stroke-linecap="round"
								stroke-linejoin="round"
								stroke-width="2"
								d="M4 6h16M4 12h16M4 18h16" />
						<path :class="{'hidden': ! open, 'inline-flex': open }"
								class="hidden"
								stroke-linecap="round"
								stroke-linejoin="round"
								stroke-width="2"
								d="M6 18L18 6M6 6l12 12" />
					</svg>
				</button>
			</div>
		</div>
	</div>
	
	<!-- Responsive Navigation Menu -->
	<div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
		<div class="pt-2 pb-3 space-y-1">
			<x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
				{{ __('khairat.dashboard') }}
			</x-responsive-nav-link>
			
			@if(Auth::user()->is_admin)
				<x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
					{{ __('khairat.admin_dashboard') }}
				</x-responsive-nav-link>
				
				<x-responsive-nav-link :href="route('admin.payment-verification')"
						:active="request()->routeIs('admin.payment-verification')">
					{{ __('khairat.payment_verification') }}
				</x-responsive-nav-link>
				
				<x-responsive-nav-link :href="route('admin.members')" :active="request()->routeIs('admin.members')">
					{{ __('khairat.members_management') }}
				</x-responsive-nav-link>
				
				<x-responsive-nav-link :href="route('admin.reports')" :active="request()->routeIs('admin.reports')">
					{{ __('khairat.reports') }}
				</x-responsive-nav-link>
				
				<x-responsive-nav-link :href="route('admin.settings')" :active="request()->routeIs('admin.settings')">
					{{ __('khairat.settings') }}
				</x-responsive-nav-link>
			@endif
			
			<x-responsive-nav-link :href="route('member.show')" :active="request()->routeIs('member.show')">
				{{ __('khairat.my_profile') }}
			</x-responsive-nav-link>
			
			<x-responsive-nav-link :href="route('dependent.index')" :active="request()->routeIs('dependent.*')">
				{{ __('khairat.family_members') }}
			</x-responsive-nav-link>
			
			<x-responsive-nav-link :href="route('payments.create')" :active="request()->routeIs('payments.create')">
				{{ __('khairat.yuran') }}
			</x-responsive-nav-link>
			
			<x-responsive-nav-link :href="route('payments.index')" :active="request()->routeIs('payments.index')">
				{{ __('khairat.sejarah_pembayaran') }}
			</x-responsive-nav-link>
			
			{{--            <x-responsive-nav-link :href="route('messages.index', [], false)" :active="request()->routeIs('messages.*')">--}}
			{{--                {{ __('Mesej') }}--}}
			{{--            </x-responsive-nav-link>--}}
		</div>
		
		<!-- Responsive Settings Options -->
		<div class="pt-4 pb-1 border-t border-gray-200">
			<div class="px-4">
				<div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
				<div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
			</div>
			
			<!-- Language Switcher for Mobile -->
			<div class="mt-3 px-4">
				<div class="font-medium text-sm text-gray-500 mb-2">{{ __('khairat.language') }}</div>
				<div class="flex space-x-4">
					<a href="{{ route('language.switch', ['locale' => 'ms']) }}" class="text-sm {{ app()->getLocale() == 'ms' ? 'font-bold text-indigo-600' : 'text-gray-600' }}">
						{{ __('khairat.malay_language') }}
					</a>
					<a href="{{ route('language.switch', ['locale' => 'en']) }}" class="text-sm {{ app()->getLocale() == 'en' ? 'font-bold text-indigo-600' : 'text-gray-600' }}">
						{{ __('khairat.english_language') }}
					</a>
				</div>
			</div>
			
			<div class="mt-3 space-y-1">
				<x-responsive-nav-link :href="route('profile.edit')">
					{{ __('khairat.profile') }}
				</x-responsive-nav-link>
				
				<x-responsive-nav-link :href="route('notifications.preferences')">
					{{ __('khairat.notification_preferences') }}
				</x-responsive-nav-link>
				
				<!-- Authentication -->
				<form method="POST" action="{{ route('logout') }}">
					@csrf
					
					<x-responsive-nav-link :href="route('logout')"
							onclick="event.preventDefault();
                                        this.closest('form').submit();">
						{{ __('khairat.logout') }}
					</x-responsive-nav-link>
				</form>
			</div>
		</div>
	</div>
</nav>
