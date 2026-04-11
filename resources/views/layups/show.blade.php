<x-app-layout>
  <div
    x-data="layupManager({{ $layup->id }})"
    x-init="init()"
    class="max-w-7xl mx-auto py-6 space-y-6"
  >

         <!-- navlink -->
     <div class="flex items-center text-sm text-gray-500 space-x-2">
        <a href="/suppliers" class="hover:text-gray-700 font-medium underline transition">
            Suppliers
        </a>

        <span class="text-gray-400">/</span>

        <a href="/suppliers/{{ $layup->supplier->id }}" x-text="layups?.supplier?.name" class="hover:text-gray-700 underline font-medium transition">
        </a>

        <span class="text-gray-400">/</span>

        <span class="text-gray-800 font-medium" x-text="layups?.name"></span>
      </div>
      <!-- header -->
      <div class="bg-white rounded-xl shadow p-6">
          <div class="flex justify-between items-start">

              <div>
                  <h1 class="text-2xl font-bold text-gray-800">
                      Layup Specification:
                      <span x-text="layups.name"></span>

                      <span class="ml-2 px-2 py-1 text-xs rounded-full"
                          :class="statusClass(layups.status)"
                          x-text="statusLabel(layups.status)">
                      </span>
                  </h1>

                  <p class="text-sm text-gray-500 mt-1">
                      Standard composite structural layering system
                  </p>
              </div>

              <div class="flex gap-2">
                  <button @click="duplicate()"
                      class="px-3 py-2 border rounded-lg text-sm hover:bg-gray-50">
                      Duplicate
                  </button>

                  <button @click="saveLayup()"
                      class="px-3 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">
                      Save Changes
                  </button>
              </div>
          </div>

          <!-- META -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 text-sm">
              <div class="p-4 border rounded-lg">
                  <p class="text-gray-400">CREATED BY</p>
                  <p class="font-medium" x-text="layups.created_by ?? '-'"></p>
              </div>

              <div class="p-4 border rounded-lg">
                  <p class="text-gray-400">LAST MODIFIED</p>
                  <p class="font-medium" x-text="$helper.formatDate(layups.updated_at)"></p>
              </div>

              <div class="p-4 border rounded-lg">
                  <p class="text-gray-400">TOTAL THICKNESS</p>
                  <p class="font-bold text-green-600" x-text="layups.total_thickness ? layups.total_thickness + ' mm' : '-' "></p>
              </div>

              <div class="p-4 border rounded-lg">
                  <p class="text-gray-400">TOTAL LAYERS</p>
                  <p class="font-bold text-green-600" x-text="layups.ply_count ? layups.ply_count + ' Layers' : '-'"></p>
              </div>
          </div>
      </div>

      <!-- body -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

          <div class="lg:col-span-2 bg-white rounded-xl shadow p-6">

              <div class="flex justify-between items-center mb-4">
                  <h2 class="text-lg font-semibold">Layer Composition</h2>

                  <button @click="openAddModal()"
                      class="px-3 py-2 bg-green-600 text-white rounded-lg text-sm">
                      + Add Layer
                  </button>
              </div>

              <div x-show="loading" class="text-center py-6 text-gray-400">
                  Loading layers...
              </div>

              <!-- table -->
              <div x-show="!loading" class="overflow-x-auto">
                <!-- <pre x-text="JSON.stringify(layups.layers, null, 2)"></pre> -->
                  <table class="w-full text-sm border rounded-lg overflow-hidden">

                      <thead class="bg-gray-100 text-gray-600">
                          <tr>
                              <th class="p-2 text-left">#</th>
                              <th class="p-2 text-left">Thickness</th>
                              <th class="p-2">Width</th>
                              <th class="p-2">Angle</th>
                              <th class="p-2">Grade</th>
                              <th class="p-2 text-right">Actions</th>
                          </tr>
                      </thead>

                      <tbody x-sort="handleSort($item, $position)" class="divide-y">
                        <!-- <tbody x-sort="handleSort($item, $position)"> -->
                          <template x-for="(layer, i) in layups?.layers" :key="layer.id">

                              <tr  x-sort:item="layer"  class="border-t hover:bg-gray-50">

                                    <td class="p-2 cursor-move" x-sort:handle>
                                        <div class="w-5 h-6 text-sm bg-gray-100 rounded flex items-center justify-center">
                                            <i class="fa-solid fa-grip-vertical text-gray-400"></i>
                                            <!-- <span x-text="layer.layer_order"></span> -->
                                        </div>
                                    </td>

                                  <td class="p-2" x-text="layer.thickness + ' mm'"></td>

                                  <td class="p-2 text-center" x-text="layer.width + ' mm'"></td>

                                  <td class="p-2 text-center">
                                      <span class="px-2 py-1 rounded bg-gray-100"
                                            x-text="layer.angle + '°'"></span>
                                  </td>

                                  <td class="p-2 text-center" x-text="layer.grade"></td>

                                  <td class="p-2 text-right space-x-2">

                                      <button @click="editLayer(layer)"
                                          class="text-blue-600 hover:text-blue-800">
                                          <i class="fa-solid fa-pen-to-square"></i>
                                      </button>

                                      <button @click="confirmDelete(layer)"
                                          class="text-red-600 hover:text-red-800">
                                          <i class="fa-solid fa-trash-can"></i>
                                      </button>

                                  </td>

                              </tr>

                          </template>
                      </tbody>
                  </table>
              </div>

              <!-- EMPTY -->
              <div x-show="!loading && layups?.layers.length === 0"
                  class="text-center py-10 text-gray-400">
                  No layers found
              </div>

              <!-- NOTE -->
              <div class="mt-6 p-4 bg-yellow-50 border rounded-lg text-sm text-gray-600">
                  <strong>Engineering Note:</strong>
                  Validate cross-grain alignment for 0°/90° alternating structure.
              </div>

          </div>

          <!-- vizualizer -->
          <div class="bg-white rounded-xl shadow p-6">

              <div class="flex justify-between mb-4">
                  <h2 class="text-lg font-semibold">Structure Visualizer</h2>

                  <div class="text-xs text-gray-500">
                      0° / 90°
                  </div>
              </div>

              <div class="space-y-2">

                  <template x-for="(layer, i) in revOrderLayers" :key="layer.id">

                      <div class="relative rounded-lg p-3 text-center text-sm font-medium shadow-sm"
                          :class="layer.angle === 90
                              ? 'bg-amber-200'
                              : 'bg-orange-100'">

                          <span x-text="`L${i+1} • ${layer.thickness}mm`"></span>

                          <span class="absolute right-2 top-2 text-xs text-gray-600"
                                x-text="layer.angle + '°'"></span>

                      </div>

                  </template>

              </div>

              <div class="mt-4 flex justify-between text-xs text-gray-500">
                  <span>Longitudinal (0°)</span>
                  <span>Transverse (90°)</span>
              </div>

          </div>

      </div>
      
      @include('layups.partials.layer-modal')
      @include('layups.partials.delete-modal')

  </div>
</x-app-layout>