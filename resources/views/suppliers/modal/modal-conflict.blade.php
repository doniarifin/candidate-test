<x-modal x-data="conflictManager()" maxWidth="6xl" name="modal-conflict" :closeable="false"
     class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
  <!-- <div class="bg-white rounded-xl shadow-xl "> -->
    <div class="bg-white rounded-xl shadow-xl overflow-hidden">

        <!-- header -->
        <div class="flex justify-between items-center px-6 py-4 border-b">
            <div>
                <h2 class="text-lg font-semibold">
                    Conflict Resolution: Import <span class="text-gray-500">[file.csv]</span>
                </h2>
                <p class="text-xs text-gray-400">
                    Please review discrepancies between incoming data and existing records.
                </p>
            </div>

            <button type="button" @click="$helper.closeModal('modal-conflict')" class="text-gray-400 hover:text-gray-600">
              <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="flex h-[500px]">

            <!-- left panel -->
            <div class="w-1/4 border-r p-4 overflow-y-auto">

                <h3 class="text-sm font-semibold mb-3">
                    Conflicting Layups (<span x-text="conflicts?.length"></span>)
                </h3>

                <template x-for="(item, index) in conflicts" :key="item.id">
                    <div @click="select(index)"
                         :class="selected === index ? 'border-green-500 bg-green-50' : 'border-gray-200'"
                         class="border rounded-lg p-3 mb-2 cursor-pointer flex justify-between items-center">

                        <div>
                            <p class="text-sm font-medium" x-text="item.name"></p>
                            <p class="text-xs text-gray-400" x-text="item.issue"></p>
                        </div>

                        <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                    </div>
                </template>

            </div>

            <!-- rigth -->
            <div class="flex-1 p-6 overflow-y-auto">

                <h3 class="font-semibold mb-4">
                    <span x-text="current?.name"></span> Comparison
                </h3>

                <div class="grid grid-cols-2 gap-6">

                    <div class="border rounded-xl overflow-hidden">
                        <div class="p-3 border-b font-medium text-gray-700">
                            Existing Version
                        </div>

                        <table class="w-full text-sm">
                            <thead class="bg-gray-100 text-gray-500 text-xs">
                                <tr>
                                    <th class="p-2">#</th>
                                    <th>Thickness</th>
                                    <th>Width</th>
                                    <th>Angle</th>
                                </tr>
                            </thead>

                            <tbody>
                                <template x-for="(row, i) in current?.existing" :key="i">
                                    <tr class="border-t">
                                        <td class="p-2" x-text="i+1"></td>
                                        <td x-text="row.thickness"></td>
                                        <td x-text="row.width"></td>
                                        <td x-text="row.angle"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <div class="p-3">
                            <button @click="keepExisting()"
                                class="w-full border rounded-lg py-2 hover:bg-gray-100">
                                Keep Existing
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
                                    <th class="p-2">#</th>
                                    <th>Thickness</th>
                                    <th>Width</th>
                                    <th>Angle</th>
                                </tr>
                            </thead>

                            <tbody>
                                <template x-for="(row, i) in current?.importing" :key="i">
                                    <tr class="border-t"
                                        :class="isDifferent(i, row) ? 'bg-red-50' : ''">

                                        <td class="p-2" x-text="i+1"></td>
                                        <td x-text="row.thickness"></td>
                                        <td x-text="row.width"></td>
                                        <td x-text="row.angle"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <div class="p-3">
                            <button @click="acceptNew()"
                                class="w-full bg-green-600 text-white rounded-lg py-2 hover:bg-green-700">
                                Accept New
                            </button>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- footer -->
        <div class="flex justify-between items-center px-6 py-4 border-t bg-gray-50">

            <button
                type="button" 
                @click="$helper.closseModal('modal-conflict')"
                class="px-4 py-2 border rounded-lg text-gray-600">
                Cancel Import
            </button>

            <div class="text-sm text-gray-500">
                <span x-text="selected + 1"></span> of <span x-text="conflicts.length"></span>
            </div>

            <button @click="next()"
                class="px-4 py-2 text-green-600">
                Next
            </button>
        </div>

    </div>
</x-modal>