<x-app-layout> 
    <div class="py-6">
        <div x-data="supplierPage" x-init="getData()">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- header -->
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                                    Suppliers
                                </h2>
                                <p class="text-sm text-gray-500">Manage timber suppliers and material sourcing.</p>
                            </div>
                        
                            <!-- <button @click="openCreate = true"
                                class="bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700">
                                + Add Supplier
                            </button> -->
                            <button 
                                class="bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700"
                                @click="
                                    openModal('add-supplier')
                                "
                            >
                                <i class="fa-solid fa-plus"></i> Add Supplier
                            </button>
                        </div>
                    </div>

                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center justify-between">
                            <!-- search -->
                            <div class="relative w-full max-w-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                                </div>

                                <input 
                                    type="text"
                                    x-model="search"
                                    placeholder="Search suppliers by name..."
                                    class="w-full pl-10 text-sm pr-4 py-2 border border-gray-200 rounded-lg 
                                        focus:outline-none focus:ring-2 focus:ring-green-500"
                                >
                            </div>

                            <!-- actions -->
                            <div class="flex items-center gap-2 ml-4">

                                <!-- filter -->
                                <button 
                                    class="flex items-center gap-2 px-4 text-sm py-2 border rounded-lg 
                                        text-gray-600 hover:bg-gray-50 transition"
                                >
                                    <i class="fa-solid fa-filter"></i>
                                    Filter
                                </button>

                                <!-- export -->
                                <button 
                                    @click="exportSuppliers(selectedIds)"
                                    class="flex items-center gap-2 px-4 text-sm py-2 border rounded-lg 
                                        text-gray-600 hover:bg-gray-50 transition"
                                >
                                    <i class="fa-solid fa-download"></i>
                                    Export
                                </button>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- tabel -->
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full border border-gray-200 rounded-lg">
                            <thead class="bg-gray-100 text-left text-sm text-gray-600">
                                <tr>
                                    <th class="p-2 text-center">
                                        <input 
                                            type="checkbox"
                                            @change="
                                                if ($event.target.checked) {
                                                    selectedIds = suppliers.map(s => s.id)
                                                } else {
                                                    selectedIds = []
                                                }
                                            "
                                        >
                                    </th>
                                    <th class="p-2">Name</th>
                                    <th class="p-2">Total Layups</th>
                                    <th class="p-2">Created At</th>
                                    <th class="p-2 text-center flex gap-2 justify-center">Actions</th>
                                </tr>
                            </thead>
            
                            <tbody>
                                <tr x-show="loading">
                                    <td colspan="4" class="text-center p-4">
                                        Loading data...
                                    </td>
                                </tr>
                                <template x-for="supplier in suppliers" :key="supplier.id">
                                    <tr x-show="!loading" class="border-t">
                                        <!-- checkbox -->
                                        <td class="p-2 text-center">
                                            <input 
                                                type="checkbox"
                                                :value="supplier.id"
                                                x-model="selectedIds"
                                            >
                                        </td>
                                        <td class="p-2">
                                            <a :href="'/suppliers/' + supplier.id" class="text-blue-600 underline">
                                                <span x-text="supplier?.name"></span>
                                            </a>
                                        </td>
                                        <td class="p-2" x-text="supplier.layups_count"></td>
                                        <td class="p-2" x-text="$helper.formatDate(supplier?.created_at)"></td>
                                        <td class="p-2 text-center flex gap-4 justify-center ">
                                            
                                            <!-- Edit Button -->
                                            <button
                                            >
                                                <a :href="'/suppliers/' + supplier.id" >
                                                   <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                                </a>
                                            </button>

                                            <!-- Delete Button -->
                                            <button 
                                                @click="
                                                    openDeleteModal(supplier.id, supplier.name)
                                                "
                                                class="text-red-600">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>

                                        </td>
                                    </tr>
                                </template>
                                <template x-if="suppliers.length === 0">
                                    <tr>
                                        <td colspan="7" class="text-center p-4 text-gray-500">
                                            No data
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
            
                    <div class="mt-4">
                        {{ $suppliers->links() }}
                    </div>

                </div>
            </div>


            <!-- modal add -->
            <x-modal name="add-supplier" maxWidth="md" :closeable="false">

                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold text-gray-800">
                        Add Supplier
                    </h2>

                    <button 
                        type="button" 
                        @click="$dispatch('close')"
                        class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
    
                <form @submit.prevent="submitCreate" class="flex flex-col max-h-[70vh]">
                    <div class="p-6 space-y-4 overflow-y-auto">
                          <x-input
                            label="Supplier Name"
                            name="name"
                            required="true"
                            placeholder="e.g. PT Kayu Jaya"
                            :model="'form.name'"
                          >
                          </x-input>
                          <x-input
                            label="Supplier Code"
                            required="true"
                            name="code"
                            placeholder="e.g. SUP-001"
                            :model="'form.code'"
                          >
                          </x-input>
                          <x-input
                            label="Supplier Email"
                            name="email"
                            placeholder="e.g. test@example.com"
                            :model="'form.email'"
                          >
                          </x-input>
                          <x-input
                            label="Supplier Location"
                            name="location"
                            placeholder="e.g. Jakarta, Indonesia"
                            :model="'form.location'"
                          >
                          </x-input>
                          <x-input
                            label="Supplier Certifications"
                            name="certifications"
                            placeholder="e.g. SPF No. 12"
                            :model="'form.certifications'"
                          >
                          </x-input>

                        <x-input-select 
                            label="Supplier Status"
                            name="status"
                            model="form.status"
                            :options="[
                                'active' => 'Active',
                                'inactive' => 'Inactive'
                            ]"
                        />
                    </div>

                    <div class="flex justify-end gap-2 p-4 border-t">
                        <button 
                            type="button"
                            @click="$dispatch('close')"
                            class="border px-4 py-2 rounded">
                            Cancel
                        </button>

                        <button class="bg-green-600 text-white px-4 py-2 rounded">
                            Save
                        </button>
                    </div>

                </form>

            </x-modal>

            <!-- modal delete -->
             <x-modal name="delete-modal" maxWidth="md" :closeable="false">

                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <span class="text-red-600">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </span>
                        Delete Supplier
                    </h2>

                    <button 
                        type="button" 
                        @click="$dispatch('close')"
                        class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-6">
                    <div class="mb-4">
                        <div class="text-gray-600">
                            Are you sure you want to delete 
                            <span class="font-semibold text-gray-800" x-text="deleteName"></span>?
                        </div>

                        <div class="text-xs text-gray-400 mt-2">
                            * This action cannot be undone.
                        </div>
                    </div>

                    <!-- ACTION -->
                    <div class="flex justify-end gap-2 mt-6">
                        <button 
                            type="button" 
                            @click="$dispatch('close')"
                            class="px-4 py-2 rounded-lg border text-gray-600 hover:bg-gray-100 transition">
                            Cancel
                        </button>

                        <form :action="'/suppliers/' + deleteId" method="POST">
                            @csrf
                            @method('DELETE')

                            <button 
                                class="px-4 py-2 rounded-lg bg-red-600 text-white 
                                    hover:bg-red-700 transition shadow">
                                Yes, Delete
                            </button>
                        </form>
                    </div>
                </div>
            </x-modal>
        </div>
    </div>
</x-app-layout>