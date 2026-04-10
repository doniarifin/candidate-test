<x-app-layout>
<div x-data="supplierPage({{ $supplier->id }})" x-init="init()" class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

      <!-- navlink -->
       <div class="flex items-center text-sm text-gray-500 space-x-2">
        <a href="/suppliers" class="hover:text-gray-700 font-medium transition">
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
                      openModal('edit-supplier');
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
                  <p class="font-medium" x-text="formatDate(supplier?.updated_at)"></p>

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
                  <button class="px-3 py-2 border rounded-lg text-sm">Import</button>
                  <button class="px-3 py-2 border rounded-lg text-sm">Export</button>
                  <button @click="openModal('add-layup')" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm">
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
                    <template x-for="layup in supplier?.layups" :key="supplier.id">
                        <tr x-show="!loading" class="border-t">
                            <!-- checkbox -->
                            <td class="p-2 text-left">
                                <input 
                                    type="checkbox"
                                    :value="layup.id"
                                    x-model="selectedIds"
                                >
                            </td>
                            <td class="p-2 text-left">
                                <a :href="'/suppliers/' + supplier.id" class="text-blue-600 underline">
                                    <span x-text="layup?.name"></span>
                                </a>
                            </td>
                            <td class="p-2 text-left" x-text="layup.code"></td>
                            <td class="p-2 text-center">
                              <span x-text="layup.total_thickness + ' mm'"></span>
                            </td>

                            <td class="p-2 text-center" >
                              <span class="px-2 py-1 bg-gray-200 rounded" x-text="layup.ply_count">
                              </span>
                            </td>
                            <td class="p-2 text-center" x-text="layup.grade"></td>
                            <td class="p-2 text-center" x-text="layup.revision"></td>
                            <td class="p-2 text-center">
                              <span 
                                  class="px-3 py-1 rounded-full"
                                  :class="{
                                      'bg-green-100 text-green-700': layup.status === 'active',
                                      'bg-yellow-100 text-yellow-700': layup.status === 'draft',
                                      'bg-gray-200 text-gray-600': layup.status === 'archived'
                                  }"
                                  x-text="
                                      layup.status === 'active' ? 'Active' :
                                      layup.status === 'draft' ? 'Draft' :
                                      'Archived'
                                  "
                              ></span>
                          </td>
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
                                        openDeleteModal(layup.id, layup.name)
                                    "
                                    class="text-red-600">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>

                            </td>
                        </tr>
                    </template>
                    <template x-if="supplier?.layups.length === 0">
                      <tr>
                          <td colspan="7" class="text-center p-4 text-gray-500">
                              No layups found
                          </td>
                      </tr>
                    </template>
                </tbody>
              </table>
          </div>

      </div>

      <!-- modal edit supplier -->
      <x-modal name="edit-supplier" maxWidth="md" :closeable="false">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-800">
                Add Supplier
            </h2>

            <button 
                type="button" 
                @click="$dispatch('close')"
                class="text-gray-400 hover:text-gray-600 transition">
                <i class="fa-solid fa-x"></i>
            </button>
        </div>

        <form @submit.prevent="updateData" class="flex flex-col max-h-[70vh]">
          <!-- body -->
          <div class="p-6 overflow-y-auto">
              <!-- <form :action="'/suppliers/' + editData.id" method="POST"> -->

                  @csrf
                  <!-- @method('PUT') -->

                  <div class="mb-4">
                      <label class="block text-sm text-gray-600 mb-1">
                          Supplier Name
                      </label>
                      <input 
                          type="text" 
                          name="name"
                          placeholder="e.g. PT Kayu Jaya"
                          x-model="editData.name"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 
                              focus:ring-2 focus:ring-green-500 focus:outline-none">
                  </div>

                  <div class="mb-4">
                      <label class="block text-sm text-gray-600 mb-1">
                          Supplier Code
                      </label>
                      <input 
                          type="text" 
                          name="code"
                          placeholder="e.g. SUP-001"
                          x-model="editData.code"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 
                              focus:ring-2 focus:ring-green-500 focus:outline-none">
                  </div>

                  <div class="mb-4">
                      <label class="block text-sm text-gray-600 mb-1">
                          Supplier Email
                      </label>
                      <input 
                          type="text" 
                          name="email"
                          placeholder="e.g. SUP-001"
                          x-model="editData.email"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 
                              focus:ring-2 focus:ring-green-500 focus:outline-none">
                  </div>

                  <div class="mb-4">
                      <label class="block text-sm text-gray-600 mb-1">
                          Supplier Location
                      </label>
                      <input 
                          type="text" 
                          name="location"
                          placeholder="e.g. Jakarta, Indonesia"
                          x-model="editData.location"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 
                              focus:ring-2 focus:ring-green-500 focus:outline-none">
                  </div>
                  <div class="mb-4">
                      <label class="block text-sm text-gray-600 mb-1">
                          Supplier Certifications
                      </label>
                      <input 
                          type="text" 
                          name="certifications"
                          placeholder="e.g. SPF No. 12"
                          x-model="editData.certifications"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 
                              focus:ring-2 focus:ring-green-500 focus:outline-none">
                  </div>
                  <div class="mb-4">
                    <label class="block text-sm text-gray-600 mb-1">
                        Supplier Status
                    </label>

                    <select 
                        name="status"
                        x-model="editData.status"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 
                              focus:ring-2 focus:ring-green-500 focus:outline-none bg-white"
                    >
                        <!-- <option value="">Select status</option> -->
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                  </div>

                  <div class="mb-4">
                      <label class="block text-sm text-gray-600 mb-1">
                          Created At
                      </label>
                      <input 
                          type="text" 
                          disabled
                          name="created_at"
                          :value="formatDate(editData?.created_at)"
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
                <i class="fa-solid fa-x"></i>
            </button>
        </div>

        <form @submit.prevent="createLayup" class="flex flex-col max-h-[70vh]">
          <!-- body -->
          <div class="p-6 overflow-y-auto">
            @csrf

            <div class="mb-4">
                <label class="block text-sm text-gray-600 mb-1">
                    Name
                </label>
                <input 
                    type="text" 
                    name="name"
                    placeholder="e.g. Standard 3-Ply Wall"
                    x-model="layup.name"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 
                        focus:ring-2 focus:ring-green-500 focus:outline-none">
                <p class="text-red-500 text-sm" x-text="errors?.name?.[0]"></p>
                
            </div>

            <div class="mb-4">
                <label class="block text-sm text-gray-600 mb-1">
                    Code
                </label>
                <input 
                    type="text" 
                    name="code"
                    placeholder="e.g. L-2021-A"
                    x-model="layup.code"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 
                        focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <div class="mb-4">
                <label class="block text-sm text-gray-600 mb-1">
                    Grade
                </label>
                <input 
                    type="text" 
                    name="grade"
                    placeholder="e.g. Spruce No. 1"
                    x-model="layup.grade"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 
                        focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <div class="mb-4">
                <label class="block text-sm text-gray-600 mb-1">
                    Revision
                </label>
                <input 
                    type="text" 
                    name="revision"
                    placeholder="e.g. Revision 5"
                    x-model="layup.revision"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 
                        focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>
            <div class="mb-4">
              <label class="block text-sm text-gray-600 mb-1">
                  Status
              </label>

              <select 
                  name="status"
                  x-model="layup.status"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 
                        focus:ring-2 focus:ring-green-500 focus:outline-none bg-white"
              >
                  <!-- <option value="">Select status</option> -->
                  <option value="draft">Draft</option>
                  <option value="active">Active</option>
                  <option value="archived">Archived</option>
              </select>
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
    </div>
</div>
</x-app-layout>