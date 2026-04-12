<x-app-layout>
<div x-data="supplierPage({{ $supplier->id }})" x-init="init()" class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

      <!-- navlink -->
       <div class="flex items-center text-sm text-gray-500 space-x-2">
        <a href="/suppliers" class="hover:text-gray-700 font-medium underline transition">
            Suppliers
        </a>

        <span class="text-gray-400">/</span>

        <span class="text-gray-800 font-medium" x-text="supplier?.name"></span>
      </div>

      <!-- header -->
      <div class="bg-white shadow rounded-xl p-6">
          <div class="flex justify-between items-center">
              <div>
                  <h1 class="text-2xl font-bold text-gray-800">
                      <span x-text="supplier?.name"></span>
                      
                      <span 
                          class="ml-2 px-3 py-1 text-sm rounded-full"
                          :class="supplier?.status === 'active' 
                              ? 'bg-green-100 text-green-700' 
                              : 'bg-gray-200 text-gray-600'"
                          x-text="supplier?.status === 'active' ? 'Active Partner' : 'Inactive Partner'"
                      ></span>
                  </h1>
                  <p class="text-sm text-gray-500 mt-1">
                      ID: <span x-text="supplier?.code"></span>
                  </p>
              </div>

              <button 
                  @click="
                      $helper.openModal('edit-supplier');
                      editData = JSON.parse(JSON.stringify(supplier))
                  "
                  class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-50"
              >
                  <i class="fa-solid fa-pen-to-square"></i> Edit Supplier
              </button>
          </div>

          <!-- info -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6 text-sm">
              <div class="border rounded-lg p-4">
                  <p class="text-gray-400">PRIMARY CONTACT</p>
                  <p class="font-medium" x-text="supplier?.email ?? '-'"></p>
              </div>

              <div class="border rounded-lg p-4">
                  <p class="text-gray-400">LOCATION</p>
                  <!-- <p class="font-medium">{{ $supplier->location }}</p> -->
                  <p class="font-medium" x-text="supplier?.location ?? '-'"></p>

              </div>

              <div class="border rounded-lg p-4">
                  <p class="text-gray-400">MATERIAL CERTIFICATIONS</p>
                  <!-- <p class="font-medium">{{ $supplier->certifications }}</p> -->
                  <p class="font-medium" x-text="supplier?.certifications ?? '-'"></p>

              </div>

              <div class="border rounded-lg p-4">
                  <p class="text-gray-400">LAST AUDIT</p>
                  <!-- <p class="font-medium">{{ $supplier->updated_at->format('M d, Y') }}</p> -->
                  <p class="font-medium" x-text="$helper.formatDate(supplier?.updated_at)"></p>

              </div>
          </div>
      </div>

      <!-- layups -->
      <div class="bg-white shadow rounded-xl p-6">
          <div class="flex justify-between items-center mb-4">
              <h2 class="text-lg font-semibold text-gray-800">
                  Associated Layups
              </h2>

              <div class="flex gap-2">
                  <button @click="$helper.openModal('modal-upload')" class="px-3 py-2 border rounded-lg text-sm">Import</button>
                  <button @click="exportLayups(selectedIds)" class="px-3 py-2 border rounded-lg text-sm">Export</button>
                  <button @click="$helper.openModal('add-layup')" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm">
                      + Add Layup
                  </button>
              </div>
          </div>

          <div class="overflow-x-auto">
              <table class="w-full border rounded-lg text-sm">
                  <thead class="bg-gray-100 text-gray-600">
                      <tr>
                          <th class="p-2 text-left">
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
                          <th class="p-2 text-left">Layout ID</th>
                          <th class="p-2 text-left">Name</th>
                          <th class="p-2">Thickness</th>
                          <th class="p-2">Ply</th>
                          <th class="p-2">Grade</th>
                          <th class="p-2">Revision</th>
                          <th class="p-2">Status</th>
                          <th class="p-2 text-center flex gap-2 justify-center">Actions</th>
                      </tr>
                  </thead>

                  <tbody>
                    <template x-show="loading">
                      <tr>
                          <td colspan="7" class="text-center p-4">
                              Loading data...
                          </td>
                      </tr>
                    </template>
                    @foreach ($layups as $layup)
                        <tr class="border-t">

                            <!-- checkbox -->
                            <td class="p-2 text-left">
                                <input 
                                    type="checkbox"
                                    :value="{{ $layup->id }}"
                                    x-model="selectedIds"
                                >
                            </td>

                            <!-- name -->
                            <td class="p-2 text-left">
                                <a href="/layups/{{ $layup->id }}" class="text-blue-600 underline">
                                    {{ $layup->name }}
                                </a>
                            </td>

                            <!-- code -->
                            <td class="p-2 text-left">
                                {{ $layup->code }}
                            </td>

                            <!-- thickness -->
                            <td class="p-2 text-center">
                                {{ $layup->total_thickness ?? 0 }} mm
                            </td>

                            <!-- ply -->
                            <td class="p-2 text-center">
                                <span class="px-2 py-1 bg-gray-200 rounded">
                                    {{ $layup->ply_count ?? 0 }}
                                </span>
                            </td>

                            <!-- grade -->
                            <td class="p-2 text-center">
                                {{ $layup->grade ?? '-' }}
                            </td>

                            <!-- revision -->
                            <td class="p-2 text-center">
                                {{ $layup->revision ?? '-' }}
                            </td>

                            <!-- status -->
                            <td class="p-2 text-center">
                                <span class="
                                    px-3 py-1 rounded-full
                                    {{ $layup->status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $layup->status === 'draft' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $layup->status === 'archived' ? 'bg-gray-200 text-gray-600' : '' }}
                                ">
                                    {{ ucfirst($layup->status) }}
                                </span>
                            </td>

                            <!-- actions -->
                            <td class="p-2 text-center flex gap-4 justify-center">

                                <!-- open -->
                                <a href="/layups/{{ $layup->id }}">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>

                                <!-- edit -->
                                <button
                                    @click="
                                        openEditModal('edit-layup')
                                        editData = {{ $layup }}
                                    "
                                >
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>

                                <!-- delete -->
                                <button 
                                    @click="
                                        openDeleteModal({{ $layup->id }}, '{{ $layup->name }}')
                                    "
                                    class="text-red-600"
                                >
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>

                            </td>

                        </tr>
                    @endforeach
                    @if ($layups->count() === 0)
                    <tr>
                        <td colspan="9" class="text-center p-4 text-gray-500">
                            No layups found
                        </td>
                    </tr>
                    @endif
                </tbody>
              </table>
          </div>
          
            <div class="mt-4">
                {{ $layups->links('pagination::tailwind') }}
            </div>

      </div>

      <!-- modal edit supplier -->
      <x-modal name="edit-supplier" maxWidth="md" :closeable="false">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-800">
                Edit Supplier
            </h2>

            <button 
                type="button" 
                @click="$dispatch('close')"
                class="text-gray-400 hover:text-gray-600 transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form @submit.prevent="updateData" class="flex flex-col max-h-[70vh]">
          <!-- body -->
          <div class="p-6 overflow-y-auto">
              <!-- <form :action="'/suppliers/' + editData.id" method="POST"> -->

                  @csrf
                  <!-- @method('PUT') -->

                  <x-input
                    class="mb-4"
                    label="Supplier Name"
                    name="name"
                    required="true"
                    placeholder="e.g. PT Kayu Jaya"
                    :model="'editData.name'"
                  />
                  <x-input
                    class="mb-4"
                    label="Supplier Code"
                    name="code"
                    required="true"
                    placeholder="e.g. PT Kayu Jaya"
                    :model="'editData.code'"
                  />
                  <x-input
                    class="mb-4"
                    label="Supplier Email"
                    name="email"
                    placeholder="e.g. PT Kayu Jaya"
                    :model="'editData.email'"
                  />
                  <x-input
                    class="mb-4"
                    label="Supplier Location"
                    name="location"
                    placeholder="e.g. PT Kayu Jaya"
                    :model="'editData.location'"
                  />
                  <x-input
                    class="mb-4"
                    label="Supplier Certifications"
                    name="certifications"
                    placeholder="e.g. PT Kayu Jaya"
                    :model="'editData.certifications'"
                  />

                  <x-input-select 
                      label="Supplier Status"
                      name="status"
                      model="editData.status"
                      :options="[
                          'active' => 'Active',
                          'inactive' => 'Inactive'
                      ]"
                  />

                  <div class="mb-4">
                      <label class="block text-sm text-gray-600 mb-1">
                          Created At
                      </label>
                      <input 
                          type="text" 
                          disabled
                          name="created_at"
                          :value="$helper.formatDate(editData?.created_at)"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 
                              focus:ring-2 focus:ring-green-500 focus:outline-none bg-gray-200"
                      >
                  </div>
          </div>

          <!-- footer -->
          <div class="flex items-center justify-end px-6 py-4 border-t">
              <div class="flex justify-end gap-2">
                <button 
                    type="button" 
                    @click="$dispatch('close')"
                    class="px-4 py-2 rounded-lg border text-gray-600 hover:bg-gray-100 transition">
                    Cancel
                </button>

                <button 
                    class="px-4 py-2 rounded-lg bg-green-600 text-white 
                        hover:bg-green-700 transition shadow">
                    Save
                </button>
            </div>
          </div>
        </form>
      </x-modal>

      <!-- modal add layup -->
      <x-modal name="add-layup" maxWidth="md" :closeable="false">
        <div class="flex items-center justify-between px-6 py-4 border-b">
          <div>
            <h2 class="text-lg font-semibold text-gray-800">
                Add Layup
            </h2>
              <span class="text-sm" x-text="supplier?.name"></span>
          </div>

            <button 
                type="button" 
                @click="$dispatch('close')"
                class="text-gray-400 hover:text-gray-600 transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form @submit.prevent="createLayup" class="flex flex-col max-h-[70vh]">
          <!-- body -->
          <div class="p-6 overflow-y-auto">
            @csrf

            <x-input
              required="true"
              class="mb-4"
              label="Name"
              name="name"
              :model="'layup.name'"
            />
            <x-input
              required="true"
              class="mb-4"
              label="Code"
              name="code"
              :model="'layup.code'"
            />
            <x-input
              class="mb-4"
              label="Grade"
              name="grade"
              :model="'layup.grade'"
            />
            <x-input
              class="mb-4"
              label="Revision"
              name="revision"
              :model="'layup.revision'"
            />
            <x-input-select 
              label="Status"
              name="status"
              model="layup.status"
              :options="[
                  'draft' => 'Draft',
                  'active' => 'Active',
                  'archived' => 'Archived'
              ]"
            />
          </div>

          <!-- footer -->
          <div class="flex items-center justify-end px-6 py-4 border-t">
              <div class="flex justify-end gap-2">
                <button 
                    type="button" 
                    @click="$dispatch('close')"
                    class="px-4 py-2 rounded-lg border text-gray-600 hover:bg-gray-100 transition">
                    Cancel
                </button>

                <button 
                    class="px-4 py-2 rounded-lg bg-green-600 text-white 
                        hover:bg-green-700 transition shadow">
                    Save
                </button>
            </div>
          </div>
        </form>
      </x-modal>

      <!-- modal edit layup -->
      <x-modal name="edit-layup" maxWidth="md" :closeable="false">
        <div class="flex items-center justify-between px-6 py-4 border-b">
          <div>
            <h2 class="text-lg font-semibold text-gray-800">
                Edit Layup
            </h2>
              <span class="text-sm" x-text="supplier?.name"></span>
          </div>

            <button 
                type="button" 
                @click="$dispatch('close')"
                class="text-gray-400 hover:text-gray-600 transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form @submit.prevent="updateLayup" class="flex flex-col max-h-[70vh]">
          <!-- body -->
          <div class="p-6 overflow-y-auto">
            @csrf

            <x-input
              required="true"
              class="mb-4"
              label="Name"
              name="name"
              model="editData.name"
            />
            <x-input
              required="true"
              class="mb-4"
              label="Code"
              name="code"
              model="editData.code"
            />
            <x-input
              class="mb-4"
              label="Grade"
              name="grade"
              model="editData.grade"
            />
            <x-input
              class="mb-4"
              label="Revision"
              name="revision"
              model="editData.revision"
            />
            <x-input-select 
              label="Status"
              name="status"
              model="editData.status"
              :options="[
                  'draft' => 'Draft',
                  'active' => 'Active',
                  'archived' => 'Archived'
              ]"
            />
          </div>

          <!-- footer -->
          <div class="flex items-center justify-end px-6 py-4 border-t">
              <div class="flex justify-end gap-2">
                <button 
                    type="button" 
                    @click="$dispatch('close')"
                    class="px-4 py-2 rounded-lg border text-gray-600 hover:bg-gray-100 transition">
                    Cancel
                </button>

                <button 
                    class="px-4 py-2 rounded-lg bg-green-600 text-white 
                        hover:bg-green-700 transition shadow">
                    Save
                </button>
            </div>
          </div>
        </form>
      </x-modal>

      <!-- modal delete layup -->
      <x-modal name="delete-modal" maxWidth="md" :closeable="false">

        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-800">
                <span class="text-red-600">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </span>
                Delete Layup
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

                <form @submit.prevent="deleteLayup" >
                    @csrf

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
  
  <div x-data="importManager({{ $supplier->id }})" x-init="init()">
    @include('suppliers.modal.modal-upload')
    @include('suppliers.modal.modal-confirm')
  </div>
  <div x-data="conflictManager()" x-init="init()">
    @include('suppliers.modal.modal-conflict')
    @include('suppliers.modal.modal-warning')
  </div>
</x-app-layout>