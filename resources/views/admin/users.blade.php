<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý Thành viên - ZENTRA Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        secondary: '#475569'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 p-6 font-sans text-gray-800">
    <div class="max-w-7xl mx-auto">

        <!-- Top Bar: Hiển thị Admin -->
        <div
            class="flex flex-col md:flex-row justify-between items-center mb-8 bg-white p-5 rounded-3xl shadow-lg border border-gray-100">
            <div class="flex items-center gap-5 mb-4 md:mb-0">
                <div class="relative">
                    <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=2563eb&color=fff&size=128"
                        class="w-14 h-14 rounded-full border-4 border-blue-50 shadow-md">
                    <!-- Online Status Dot -->
                    <div class="absolute -bottom-1 -right-1 bg-green-500 w-5 h-5 rounded-full border-2 border-white"
                        title="Online"></div>
                </div>
                <div>
                    <h2 class="font-black text-xl text-gray-800">Xin chào, Admin {{ Auth::user()->name }}! 👋</h2>
                    <p class="text-sm text-gray-500 font-medium">Hệ thống ZENTRA SaaS đang hoạt động ổn định.</p>
                </div>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('admin.settings') }}"
                    class="group flex items-center gap-2 px-5 py-3 bg-gray-50 hover:bg-white border border-gray-200 rounded-xl text-gray-700 font-bold transition-all hover:shadow-md">
                    <i class="fa-solid fa-gear group-hover:rotate-90 transition-transform duration-500"></i> Cấu hình
                    API
                </a>
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 rounded-xl text-white font-bold transition-all shadow-lg shadow-blue-500/30 transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-chart-line"></i> Dashboard
                </a>
            </div>
        </div>

        <!-- Stats Quick View (Thống kê nhanh) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mb-8">
            <!-- Card 1 -->
            <div
                class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600"><i
                        class="fa-solid fa-users text-2xl"></i></div>
                <div>
                    <p class="text-xs text-gray-400 uppercase font-bold tracking-wider">Tổng User</p>
                    <p class="text-3xl font-black text-gray-800">{{ $users->total() }}</p>
                </div>
            </div>
            <!-- Card 2 -->
            <div
                class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                <div class="w-14 h-14 rounded-2xl bg-purple-50 flex items-center justify-center text-purple-600"><i
                        class="fa-solid fa-crown text-2xl"></i></div>
                <div>
                    <p class="text-xs text-gray-400 uppercase font-bold tracking-wider">Trả Phí</p>
                    <p class="text-3xl font-black text-gray-800">{{ $users->where('plan_type', '!=', 'free')->count() }}
                    </p>
                </div>
            </div>
            <!-- Card 3 -->
            <div
                class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                <div class="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center text-green-600"><i
                        class="fa-solid fa-wallet text-2xl"></i></div>
                <div>
                    <p class="text-xs text-gray-400 uppercase font-bold tracking-wider">Tổng Số dư</p>
                    <p class="text-3xl font-black text-gray-800">{{ number_format($users->sum('balance')) }}</p>
                </div>
            </div>
            <!-- Card 4 -->
            <div
                class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
                <div class="w-14 h-14 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-600"><i
                        class="fa-solid fa-bolt text-2xl"></i></div>
                <div>
                    <p class="text-xs text-gray-400 uppercase font-bold tracking-wider">Lượt quét hôm nay</p>
                    <p class="text-3xl font-black text-gray-800">{{ number_format($users->sum('daily_usage_count')) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Toolbar -->
            <div
                class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4 bg-gray-50/50">
                <h2 class="text-xl font-bold flex items-center gap-2 text-gray-800">
                    <span class="w-2 h-8 bg-blue-600 rounded-full block"></span> Danh sách thành viên
                </h2>
                <form class="flex gap-2 w-full md:w-auto relative">
                    <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" placeholder="Tìm theo tên, email..."
                        value="{{ request('search') }}"
                        class="pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none w-full md:w-72 text-sm shadow-sm transition-all hover:border-blue-300">
                    <button
                        class="bg-gray-800 text-white px-6 py-2.5 rounded-xl hover:bg-gray-900 font-bold transition-colors text-sm shadow-md">Lọc</button>
                </form>
            </div>

            @if (session('success'))
                <div
                    class="mx-6 mt-6 bg-green-50 text-green-700 p-4 rounded-xl border border-green-100 flex items-center gap-3 animate-pulse">
                    <i class="fa-solid fa-circle-check text-xl"></i> <span
                        class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead
                        class="bg-gray-50 border-b border-gray-100 text-xs uppercase text-gray-500 font-bold tracking-wider">
                        <tr>
                            <th class="p-5">Thành viên</th>
                            <th class="p-5">Gói cước hiện tại</th>
                            <th class="p-5">Thời hạn</th>
                            <th class="p-5 text-right">Ví tiền</th>
                            <th class="p-5 text-center">Quota</th>
                            <th class="p-5 min-w-[300px]">Cập nhật nhanh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($users as $u)
                            @php
                                // Check Admin
                                $isAdmin = in_array($u->email, [
                                    'admin@zentra.com',
                                    'chien24022003@gmail.com',
                                    'tranvanchien24022003@gmail.com',
                                ]);
                            @endphp
                            <tr class="hover:bg-blue-50/40 transition-colors group">
                                <td class="p-5">
                                    <div class="flex items-center gap-4">
                                        <div class="relative">
                                            <div
                                                class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-600 font-bold text-sm border border-gray-200 shadow-sm">
                                                {{ substr($u->name, 0, 1) }}
                                            </div>
                                            @if ($isAdmin)
                                                <div
                                                    class="absolute -top-1 -right-1 text-yellow-500 drop-shadow-sm text-xs">
                                                    <i class="fa-solid fa-crown"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-800 flex items-center gap-2">
                                                {{ $u->name }}
                                                @if ($isAdmin)
                                                    <span
                                                        class="bg-red-50 text-red-600 text-[9px] px-2 py-0.5 rounded-full uppercase font-black tracking-wider border border-red-100">Admin</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500 font-mono">{{ $u->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-5">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-black uppercase tracking-wide border shadow-sm
                                    {{ $u->plan_type == 'premium'
                                        ? 'bg-purple-50 text-purple-700 border-purple-100'
                                        : ($u->plan_type == 'pro'
                                            ? 'bg-blue-50 text-blue-700 border-blue-100'
                                            : ($u->plan_type == 'basic'
                                                ? 'bg-green-50 text-green-700 border-green-100'
                                                : 'bg-gray-100 text-gray-500 border-gray-200')) }}">
                                        @if ($u->plan_type != 'free')
                                            <i class="fa-solid fa-star text-[10px]"></i>
                                        @endif
                                        {{ $u->plan_type ?? 'FREE' }}
                                    </span>
                                </td>
                                <td class="p-5">
                                    @if ($u->vip_expires_at)
                                        @if (\Carbon\Carbon::parse($u->vip_expires_at)->isPast())
                                            <div class="flex items-center gap-2 text-red-500 font-bold text-sm"><i
                                                    class="fa-solid fa-circle-exclamation"></i> Đã hết hạn</div>
                                            <div class="text-xs text-gray-400 mt-1">
                                                {{ \Carbon\Carbon::parse($u->vip_expires_at)->format('d/m/Y') }}</div>
                                        @else
                                            <div class="flex items-center gap-2 text-green-600 font-bold text-sm"><i
                                                    class="fa-solid fa-clock"></i> Còn
                                                {{ ceil(now()->diffInDays($u->vip_expires_at)) }} ngày</div>
                                            <div class="text-xs text-gray-400 mt-1">Hết:
                                                {{ \Carbon\Carbon::parse($u->vip_expires_at)->format('d/m/Y') }}</div>
                                        @endif
                                    @else
                                        <div class="flex flex-col">
                                            <span class="text-gray-500 text-sm font-medium flex items-center gap-1"><i
                                                    class="fa-regular fa-hourglass"></i> Dùng thử / Free</span>
                                            @php
                                                $createdAt = $u->created_at
                                                    ? \Carbon\Carbon::parse($u->created_at)
                                                    : now();
                                                $trialEnds = $createdAt->copy()->addDays(7);
                                                $diff = now()->diffInDays($trialEnds, false);
                                            @endphp
                                            @if ($diff > 0)
                                                <span class="text-[10px] text-orange-500 font-bold mt-0.5">Còn
                                                    {{ ceil($diff) }} ngày dùng thử</span>
                                            @else
                                                <span class="text-[10px] text-red-400 font-bold mt-0.5">Đã hết dùng
                                                    thử</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="p-5 text-right font-mono font-bold text-gray-700 text-base">
                                    {{ number_format($u->balance) }} <span class="text-xs text-gray-400">đ</span>
                                </td>
                                <td class="p-5 text-center">
                                    <span
                                        class="inline-block px-3 py-1 bg-gray-100 rounded-lg font-bold text-gray-600 text-sm">{{ $u->daily_usage_count }}</span>
                                </td>
                                <td class="p-5">
                                    <form action="{{ route('admin.users.update', $u->id) }}" method="POST"
                                        class="flex flex-col gap-3">
                                        @csrf
                                        <div class="flex gap-2">
                                            <select name="plan_type"
                                                class="w-full bg-white border border-gray-200 rounded-lg px-2 py-1.5 text-xs font-medium focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none shadow-sm cursor-pointer hover:border-blue-300 transition-colors">
                                                <option value="free"
                                                    {{ $u->plan_type == 'free' ? 'selected' : '' }}>Free
                                                </option>
                                                <option value="basic"
                                                    {{ $u->plan_type == 'basic' ? 'selected' : '' }}>
                                                    Basic</option>
                                                <option value="pro" {{ $u->plan_type == 'pro' ? 'selected' : '' }}>
                                                    Pro
                                                </option>
                                                <option value="premium"
                                                    {{ $u->plan_type == 'premium' ? 'selected' : '' }}>
                                                    Premium</option>
                                            </select>
                                            <select name="add_days"
                                                class="w-full bg-white border border-gray-200 rounded-lg px-2 py-1.5 text-xs font-medium focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none shadow-sm cursor-pointer hover:border-blue-300 transition-colors">
                                                <option value="">+ Gia hạn</option>
                                                <option value="30">30 ngày</option>
                                                <option value="365">1 năm</option>
                                            </select>
                                        </div>
                                        <div class="flex gap-2">
                                            <input type="number" name="balance_change" placeholder="+/- Tiền"
                                                class="w-24 bg-white border border-gray-200 rounded-lg px-2 py-1.5 text-xs focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none shadow-sm">
                                            <button type="submit"
                                                class="flex-1 bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-blue-700 transition-all shadow-md hover:shadow-lg active:scale-95">Lưu</button>
                                        </div>
                                        <label
                                            class="flex items-center gap-2 text-xs text-red-500 font-medium cursor-pointer hover:text-red-700 transition-colors select-none">
                                            <input type="checkbox" name="reset_free" value="1"
                                                class="rounded text-red-500 focus:ring-red-500 w-3.5 h-3.5 cursor-pointer">
                                            Hủy gói (Về Free)
                                        </label>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-6 border-t border-gray-100 bg-gray-50">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</body>

</html>
