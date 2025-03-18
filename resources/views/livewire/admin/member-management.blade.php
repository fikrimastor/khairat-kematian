<div>
    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('message') }}</p>
        </div>
    @endif

    <div class="mb-6">
        <div class="flex flex-col md:flex-row justify-between gap-4 mb-4">
            <div class="flex-1">
                <label for="search" class="sr-only">{{ __('khairat.search') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" id="search" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="{{ __('khairat.search_members_placeholder') }}" type="search">
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4">
                <select wire:model.live="status" id="status" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">{{ __('khairat.all_statuses') }}</option>
                    <option value="active">{{ __('khairat.active') }}</option>
                    <option value="inactive">{{ __('khairat.inactive') }}</option>
                    <option value="expired">{{ __('khairat.expired') }}</option>
                </select>
                
                <select wire:model.live="perPage" id="perPage" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="10">10 {{ __('khairat.per_page') }}</option>
                    <option value="25">25 {{ __('khairat.per_page') }}</option>
                    <option value="50">50 {{ __('khairat.per_page') }}</option>
                    <option value="100">100 {{ __('khairat.per_page') }}</option>
                </select>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
        <table class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
            <thead>
                <tr class="text-left">
                    <th wire:click="sortBy('name')" class="bg-gray-100 sticky top-0 border-b border-gray-200 px-6 py-3 text-gray-600 font-bold tracking-wider uppercase text-xs cursor-pointer">
                        {{ __('khairat.name') }}
                        @if ($sortField === 'name')
                            <span class="ml-1">
                                @if ($sortDirection === 'asc')
                                    &#8593;
                                @else
                                    &#8595;
                                @endif
                            </span>
                        @endif
                    </th>
                    <th wire:click="sortBy('email')" class="bg-gray-100 sticky top-0 border-b border-gray-200 px-6 py-3 text-gray-600 font-bold tracking-wider uppercase text-xs cursor-pointer">
                        {{ __('khairat.email_address') }}
                        @if ($sortField === 'email')
                            <span class="ml-1">
                                @if ($sortDirection === 'asc')
                                    &#8593;
                                @else
                                    &#8595;
                                @endif
                            </span>
                        @endif
                    </th>
                    <th wire:click="sortBy('identification_number')" class="bg-gray-100 sticky top-0 border-b border-gray-200 px-6 py-3 text-gray-600 font-bold tracking-wider uppercase text-xs cursor-pointer">
                        {{ __('khairat.identification') }}
                        @if ($sortField === 'identification_number')
                            <span class="ml-1">
                                @if ($sortDirection === 'asc')
                                    &#8593;
                                @else
                                    &#8595;
                                @endif
                            </span>
                        @endif
                    </th>
                    <th wire:click="sortBy('membership_expires_at')" class="bg-gray-100 sticky top-0 border-b border-gray-200 px-6 py-3 text-gray-600 font-bold tracking-wider uppercase text-xs cursor-pointer">
                        {{ __('khairat.expiry_date') }}
                        @if ($sortField === 'membership_expires_at')
                            <span class="ml-1">
                                @if ($sortDirection === 'asc')
                                    &#8593;
                                @else
                                    &#8595;
                                @endif
                            </span>
                        @endif
                    </th>
                    <th class="bg-gray-100 sticky top-0 border-b border-gray-200 px-6 py-3 text-gray-600 font-bold tracking-wider uppercase text-xs">
                        {{ __('khairat.status') }}
                    </th>
                    <th class="bg-gray-100 sticky top-0 border-b border-gray-200 px-6 py-3 text-gray-600 font-bold tracking-wider uppercase text-xs">
                        {{ __('khairat.actions') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($members as $member)
                    <tr class="hover:bg-gray-50">
                        <td class="border-dashed border-t border-gray-200 px-6 py-4">
                            {{ $member->name }}
                        </td>
                        <td class="border-dashed border-t border-gray-200 px-6 py-4">
                            {{ $member->email }}
                        </td>
                        <td class="border-dashed border-t border-gray-200 px-6 py-4">
                            {{ $member->identification_number }}
                        </td>
                        <td class="border-dashed border-t border-gray-200 px-6 py-4">
                            @if ($member->membership_expires_at)
                                <span class="{{ $member->isMembershipExpired() ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $member->membership_expires_at->format('d M Y') }}
                                </span>
                            @else
                                <span class="text-gray-400">{{ __('khairat.not_set') }}</span>
                            @endif
                        </td>
                        <td class="border-dashed border-t border-gray-200 px-6 py-4">
                            @if ($member->isActive())
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ __('khairat.active') }}
                                </span>
                            @elseif ($member->isMembershipExpired())
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    {{ __('khairat.expired') }}
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ __('khairat.inactive') }}
                                </span>
                            @endif
                        </td>
                        <td class="border-dashed border-t border-gray-200 px-6 py-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.members.show', $member) }}" class="text-blue-600 hover:text-blue-900" title="{{ __('khairat.view_details') }}">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </a>
                                
                                <button wire:click="editMember({{ $member->id }})" class="text-indigo-600 hover:text-indigo-900" title="{{ __('khairat.edit') }}">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                    </svg>
                                </button>
                                
                                @if ($member->isActive())
                                    <button wire:click="deactivateMember({{ $member->id }})" class="text-yellow-600 hover:text-yellow-900" title="{{ __('khairat.deactivate') }}">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                @else
                                    <button wire:click="activateMember({{ $member->id }})" class="text-green-600 hover:text-green-900" title="{{ __('khairat.activate') }}">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                @endif
                                
                                <button wire:click="extendMembership({{ $member->id }})" class="text-blue-600 hover:text-blue-900" title="{{ __('khairat.extend_membership') }}">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                                
                                <button wire:click="confirmMemberDeletion({{ $member->id }})" class="text-red-600 hover:text-red-900" title="{{ __('khairat.delete') }}">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="border-dashed border-t border-gray-200 px-6 py-4 text-center text-gray-500">
                            {{ __('khairat.no_members_found') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $members->links() }}
    </div>

    <!-- Edit Member Modal -->
    @if ($editingMember)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                {{ __('khairat.edit_member') }}
                            </h3>
                            
                            <div class="mt-2 space-y-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">{{ __('khairat.name') }}</label>
                                    <input wire:model="name" type="text" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">{{ __('khairat.email_address') }}</label>
                                    <input wire:model="email" type="email" id="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">{{ __('khairat.phone_number') }}</label>
                                    <input wire:model="phone" type="text" id="phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="identification_number" class="block text-sm font-medium text-gray-700">{{ __('khairat.identification') }}</label>
                                    <input wire:model="identification_number" type="text" id="identification_number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('identification_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700">{{ __('khairat.address') }}</label>
                                    <textarea wire:model="address" id="address" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                                    @error('address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="membership_expires_at" class="block text-sm font-medium text-gray-700">{{ __('khairat.expiry_date') }}</label>
                                    <input wire:model="membership_expires_at" type="date" id="membership_expires_at" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @error('membership_expires_at') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label for="membership_type" class="block text-sm font-medium text-gray-700">{{ __('khairat.membership_type') }}</label>
                                    <select wire:model="membership_type" id="membership_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">{{ __('khairat.select_type') }}</option>
                                        <option value="standard">{{ __('khairat.standard') }}</option>
                                        <option value="premium">{{ __('khairat.premium') }}</option>
                                        <option value="lifetime">{{ __('khairat.lifetime') }}</option>
                                    </select>
                                    @error('membership_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="saveMember" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ __('khairat.save') }}
                    </button>
                    <button wire:click="closeEditModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ __('khairat.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Extend Membership Modal -->
    @if ($extendingMember)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                {{ __('khairat.extend_membership') }}
                            </h3>
                            
                            <div class="mt-2 space-y-4">
                                <p class="text-sm text-gray-500">
                                    {{ __('khairat.extend_membership_for') }} <strong>{{ $extendingMemberName }}</strong>
                                </p>
                                
                                <div>
                                    <label for="extension_months" class="block text-sm font-medium text-gray-700">{{ __('khairat.extension_duration') }}</label>
                                    <select wire:model="extensionMonths" id="extension_months" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="1">1 {{ __('khairat.month') }}</option>
                                        <option value="3">3 {{ __('khairat.months') }}</option>
                                        <option value="6">6 {{ __('khairat.months') }}</option>
                                        <option value="12">12 {{ __('khairat.months') }}</option>
                                        <option value="24">24 {{ __('khairat.months') }}</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="new_expiry_date" class="block text-sm font-medium text-gray-700">{{ __('khairat.new_expiry_date') }}</label>
                                    <input type="text" id="new_expiry_date" readonly value="{{ $newExpiryDate }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-gray-50">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="saveExtension" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ __('khairat.extend') }}
                    </button>
                    <button wire:click="closeExtendModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ __('khairat.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($confirmingMemberDeletion)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                {{ __('khairat.delete_member') }}
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    {{ __('khairat.confirm_delete_member') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="deleteMember" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ __('khairat.delete') }}
                    </button>
                    <button wire:click="closeDeleteModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ __('khairat.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div> 