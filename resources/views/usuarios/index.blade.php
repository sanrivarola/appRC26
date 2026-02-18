<x-app-layout>
    <div class="flex">
        <x-sidebar />
        <main class="flex-1 bg-gray-50 min-h-screen md:ml-64 p-4 md:p-8 transition-all duration-300">
            <div class="max-w-5xl mx-auto mt-6">
                <div class="bg-white shadow rounded-2xl p-6">

                    <h2 class="text-2xl font-semibold mb-6 flex items-center gap-2 text-rose-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-rose-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                        Usuarios
                    </h2>

                    <!-- Buscador, filtro por rol y filtro de inactivos -->
                    <form method="GET" action="{{ route('usuarios.index') }}" class="mb-6 flex flex-wrap gap-4 items-center">
                            <input
                                type="text"
                                name="buscar"
                                value="{{ request('buscar') }}"
                                placeholder="Buscar por nombre o email..."
                                class="border border-gray-300 rounded-lg px-4 py-3 text-md w-60 md:w-80 focus:outline-none focus:ring-2 focus:ring-rose-200"
                            />

                            <select name="rol" class="border border-gray-300 rounded-lg px-4 py-3 text-md w-44 md:w-60">
                                <option value="">Todos los roles</option>
                                @foreach($roles as $role)
                                    @if($role->name !== 'superadmin')
                                        <option value="{{ $role->name }}" {{ request('rol') == $role->name ? 'selected' : '' }}>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>

                        <label class="flex items-center gap-2">
                            <input
                                type="checkbox"
                                name="ver_inactivos"
                                value="1"
                                class="accent-rose-400"
                                {{ request('ver_inactivos') ? 'checked' : '' }}
                                onchange="this.form.submit()"
                            />
                            <span class="text-sm text-gray-700">Mostrar usuarios inactivos</span>
                        </label>
                        <button
                            type="submit"
                            class="bg-rose-400 text-white rounded px-4 py-2 font-semibold hover:bg-rose-500 transition"
                        >Buscar</button>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr>
                                    <th class="text-left p-2 font-bold text-gray-600">Nombre</th>
                                    <th class="text-left p-2 font-bold text-gray-600 hidden md:table-cell">Email</th>
                                    <th class="text-left p-2 font-bold text-gray-600">Rol</th>
                                    <th class="text-left p-2 font-bold text-gray-600">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                <tr class="hover:bg-indigo-50 {{ !$user->activo ? 'opacity-60' : '' }}">
                                    <td class="p-2">{{ $user->name }}</td>
                                    <td class="p-2 hidden md:table-cell">{{ $user->email }}</td>
                                    <td class="p-2">
                                        @foreach($user->roles as $role)
                                            <span class="px-2 py-1 bg-rose-400 text-white rounded text-xs">{{ ucfirst($role->name) }}</span>
                                        @endforeach
                                    </td>
                                    <td class="p-2">
                                        <a href="{{ route('usuarios.edit', $user) }}" class="text-rose-600 hover:underline">Editar</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center p-4 text-gray-500">No se encontraron usuarios.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $users->withQueryString()->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>
</x-app-layout>
