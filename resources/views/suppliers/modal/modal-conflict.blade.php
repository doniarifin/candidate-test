<x-modal x-data="conflictManager()" maxWidth="6xl" name="modal-conflict" :closeable="false"
     class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
  <!-- <div class="bg-white rounded-xl shadow-xl "> -->
    <!-- <pre x-text="JSON.stringify(conflicts, null, 2)"></pre> -->

    <div class="bg-white rounded-xl shadow-xl overflow-hidden">

        <!-- header -->
        <div class="flex justify-between items-center px-6 py-4 border-b">
            <div>
                <h2 class="text-lg font-semibold">
                    Conflict Resolution: Import <span class="text-gray-500">[file.json]</span>
                </h2>
                <p class="text-xs text-gray-400">
                    Please review discrepancies between incoming data and existing records.
                </p>
            </div>

            <button type="button" @click="closeModalConflict" class="text-gray-400 hover:text-gray-600">
              <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="flex h-[500px]">

            <!-- left panel -->
            <div class="w-1/4 border-r p-4 overflow-y-auto">
              <h3 
                  @click="showUnresolved = !showUnresolved"
                  class="text-sm font-semibold mb-3 flex justify-between items-center cursor-pointer">

                  <span>
                      Conflicting Layups (<span x-text="unresolved?.length"></span>)
                  </span>

                  <i 
                      :class="showUnresolved ? 'fa-chevron-down' : 'fa-chevron-right'"
                      class="fa-solid text-xs text-gray-400">
                  </i>
              </h3>

              <div x-show="showUnresolved" x-transition>
                <template x-for="(item, index) in unresolved" :key="item.id">
                    <div @click="select(item)"
                         :class="selectedCId === item.id ? 'border-green-500 bg-green-50' : 'border-gray-200'"
                         class="border rounded-lg p-3 mb-2 cursor-pointer flex justify-between items-center">
  
                        <div>
                            <p class="text-sm font-medium" x-text="item.name"></p>
                            <p class="text-xs text-gray-400" x-text="item.issue"></p>
                        </div>
  
                        <!-- <div class="w-2 h-2 bg-red-500 rounded-full"></div> -->
                        <div 
                          class="w-2 h-2 rounded-full"
                          :class="decisions[item.id] ? 'bg-green-500' : 'bg-red-500'">
                        </div>
                    </div>
                </template>
              </div>

                <!-- resolve -->
                <h3 
                    @click="showResolved = !showResolved"
                    class="text-sm font-semibold mb-3 flex justify-between items-center cursor-pointer">

                    <span>
                        Resolved (<span x-text="resolved?.length"></span>)
                    </span>

                    <i 
                        :class="showResolved ? 'fa-chevron-down' : 'fa-chevron-right'"
                        class="fa-solid text-xs text-gray-400">
                    </i>
                </h3>

                <div x-show="showResolved" x-transition>
                  <template x-for="(item, index) in resolved" :key="item.id">
                      <div @click="select(item)"
                           :class="selectedCId === item.id ? 'border-green-500 bg-green-50' : 'border-gray-200'"
                           class="border rounded-lg p-3 mb-2 cursor-pointer flex justify-between items-center">
  
                          <div>
                              <p class="text-sm font-medium" x-text="item.name"></p>
                              <p class="text-xs text-gray-400" x-text="item.issue"></p>
                          </div>
  
                          <!-- <div class="w-2 h-2 bg-red-500 rounded-full"></div> -->
                          <div 
                            x-show="decisions[item.id]"
                            :class="decisions[item.id] ? 'text-green-500' : 'text-red-500'">
                            
                            <i class="fa-regular fa-circle-check"></i>
                        </div>
                      </div>
                  </template>
                </div>

            </div>

            <!-- rigth -->
            <div x-show="current?.id" class="flex-1 p-6 overflow-y-auto">

                <h3 class="font-semibold mb-4">
                    <span x-text="current?.name"></span> Comparison
                </h3>

                <div class="mb-4 text-sm">
                  <template x-if="!decisions[current.id]">
                      <span class="text-gray-400">No decision selected</span>
                  </template>

                  <template x-if="decisions[current.id] === 'keep_existing'">
                      <span class="text-gray-600 font-medium">
                          Keep Existing (resolved)
                      </span>
                  </template>

                  <template x-if="decisions[current.id] === 'accept_new'">
                      <span class="text-green-600 font-medium">
                          Accept New (resolved)
                      </span>
                  </template>
              </div>

                <div class="grid grid-cols-2 gap-6">

                    <div class="border rounded-xl overflow-hidden">
                        <div class="p-3 border-b font-medium text-gray-700">
                            Existing Version
                        </div>

                        <table class="w-full text-sm">
                            <thead class="bg-gray-100 text-gray-500 text-xs">
                                <tr>
                                    <th class="text-center p-2">#</th>
                                    <th class="text-center">Thickness</th>
                                    <th class="text-center">Width</th>
                                    <th class="text-center">Angle</th>
                                </tr>
                            </thead>

                            <tbody>
                                <template x-for="(row, i) in current?.existing" :key="i">
                                    <tr class="border-t">
                                        <td class="text-center p-2" x-text="row?.layer_order"></td>
                                        <td class="text-center" x-text="row?.thickness"></td>
                                        <td class="text-center" x-text="row?.width"></td>
                                        <td class="text-center" x-text="row?.angle"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <div class="p-3">
                            <button 
                              @click="keepExisting()"
                              class="w-full border rounded-lg py-2 flex items-center justify-center gap-2"
                              :class="decisions[current.id] === 'keep_existing' 
                                  ? 'bg-gray-200 border-gray-400 font-semibold' 
                                  : 'hover:bg-gray-100'">

                              <span>Keep Existing</span>

                              <template x-if="decisions[current.id] === 'keep_existing'">
                                  <i class="fa-solid fa-check text-gray-700"></i>
                              </template>
                          </button>
                        </div>
                    </div>

                    <!-- import -->
                    <div class="border rounded-xl overflow-hidden">
                        <div class="p-3 border-b font-medium text-green-700">
                            Importing Version
                        </div>

                        <table class="w-full text-sm">
                            <thead class="bg-gray-100 text-gray-500 text-xs">
                                <tr>
                                    <th class="text-center p-2">#</th>
                                    <th class="text-center">Thickness</th>
                                    <th class="text-center">Width</th>
                                    <th class="text-center">Angle</th>
                                </tr>
                            </thead>

                            <tbody>
                                <template x-for="(row, i) in current?.importing" :key="i">
                                    <tr class="border-t"
                                        :class="isDifferent(i, row) ? 'bg-red-50' : ''">

                                        <td class="text-center p-2" x-text="row?.layer_order"></td>
                                        <td class="text-center" x-text="row?.thickness"></td>
                                        <td class="text-center" x-text="row?.width"></td>
                                        <td class="text-center" x-text="row?.angle"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <div class="p-3">
                            <button 
                                @click="acceptNew()"
                                class="w-full rounded-lg py-2 flex items-center justify-center gap-2 text-white"
                                :class="decisions[current.id] === 'accept_new' 
                                    ? 'bg-green-800 font-semibold' 
                                    : 'bg-green-600 hover:bg-green-700'">

                                <span>Accept New</span>

                                <template x-if="decisions[current.id] === 'accept_new'">
                                    <i class="fa-solid fa-check"></i>
                                </template>
                            </button>
                        </div>
                    </div>

                </div>

            </div>

            <div x-show="!current?.id" class="flex-1 flex items-center justify-center text-gray-400">
              <p>No layup selected</p>
          </div>
        </div>

        <!-- footer -->
        <div class="flex justify-between items-center px-6 py-4 border-t bg-gray-50">

            <button
                type="button" 
                @click="closeModalConflict"
                class="px-4 py-2 border rounded-lg text-gray-600">
                Cancel Import
            </button>

            <div class="text-sm text-gray-500">
                <span x-text="conflicts.findIndex(c => c.id === selectedCId) + 1"></span>
            </div>

            <button 
                type="button"
                @click="unresolved.length > 0 ? next() : finish()"
                :disabled="!current?.id && unresolved.length > 0"
                class="px-4 py-2 rounded-lg disabled:opacity-50"
                :class="unresolved.length === 0 
                    ? 'bg-green-600 text-white' 
                    : 'text-green-600'">

                <span x-text="unresolved.length > 0 ? 'Next' : 'Finish'"></span>
            </button>
        </div>

    </div>
</x-modal>