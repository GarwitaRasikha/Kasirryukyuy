<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SiteController extends Controller
{
    public function index()
    {
        $toko = (object)[
            'nama_toko' => 'SmartKasir POS',
            'alamat_toko' => 'Indonesia',
            'tlp' => '0812-3456-7890',
            'nama_pemilik' => 'Owner SmartKasir'
        ];
        
        $totalSales = 0;
        $totalTransactions = 0;
        $lowStock = 0;

        if (\Illuminate\Support\Facades\Schema::hasTable('toko')) {
            $dbToko = DB::table('toko')->first();
            if ($dbToko) {
                $toko = $dbToko;
            }
        }
        
        if (\Illuminate\Support\Facades\Schema::hasTable('nota')) {
            $totalSales = DB::table('nota')->sum('total');
            $totalTransactions = DB::table('nota')->count();
        }
        
        if (\Illuminate\Support\Facades\Schema::hasTable('barang')) {
            $lowStock = DB::table('barang')->whereRaw('CAST(stok AS SIGNED) <= 50')->count();
        }
        
        return view('home', compact('toko', 'totalSales', 'totalTransactions', 'lowStock'));
    }

    public function fitur()
    {
        return view('features');
    }

    public function tentang()
    {
        return view('about');
    }

    public function kontak()
    {
        return view('contact');
    }

    public function panduan()
    {
        return view('panduan');
    }

    public function faq()
    {
        return view('faq');
    }

    public function kirimKontak(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subjek' => 'required|string|max:255',
            'pesan' => 'required|string',
        ]);

        DB::table('pesan_kontak')->insert($data);

        Session::flash('success', 'Pesan Anda berhasil dikirim.');

        return redirect()->route('kontak');
    }

    public function loginForm()
    {
        return view('login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'user' => 'required',
            'pass' => 'required',
        ]);

        $user = trim($request->input('user'));
        $pass = trim($request->input('pass'));

        $member = DB::table('member')
            ->join('login', 'member.id_member', '=', 'login.id_member')
            ->where('login.user', $user)
            ->where('login.pass', md5($pass))
            ->select('member.*', 'login.user', 'login.pass')
            ->first();

        if ($member && (int) $member->status === 1) {
            Session::put('admin', (array) $member);
            Session::put('role', $member->role);
            Session::put('status', $member->status);

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['admin'] = (array) $member;

            return redirect()->route('dashboard');
        }

        Session::flash('error', 'Username atau password salah.');

        return redirect()->back();
    }

    public function dashboard(Request $request)
    {
        if (!Session::has('admin')) {
            return redirect()->route('login');
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['admin'])) {
            $_SESSION['admin'] = Session::get('admin');
        }

        $page = $request->query('page');

        // Handle Kasir Actions
        if ($page === 'kasir') {
            if ($request->isMethod('post') && $request->has('tambah_user')) {
                $nama = $request->input('nama');
                $alamat = $request->input('alamat');
                $telepon = $request->input('telepon');
                $email = $request->input('email');
                $nik = $request->input('nik');
                $user = $request->input('user');
                $pass = $request->input('pass');
                $role = $request->input('role');
                $status = 1;
                $nama_file = 'default.jpg';
                
                if ($request->hasFile('gambar')) {
                    $file = $request->file('gambar');
                    $nama_file = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('assets/img/user'), $nama_file);
                }
                
                $id_member = DB::table('member')->insertGetId([
                    'nm_member' => $nama,
                    'alamat_member' => $alamat,
                    'telepon' => $telepon,
                    'email' => $email,
                    'gambar' => $nama_file,
                    'NIK' => $nik,
                    'role' => $role,
                    'status' => $status
                ]);
                
                DB::table('login')->insert([
                    'user' => $user,
                    'pass' => md5($pass),
                    'id_member' => $id_member
                ]);
                
                return redirect()->route('dashboard', ['page' => 'kasir'])->with('success', 'Kasir berhasil ditambahkan.');
            }

            if ($request->has('activate') && $request->has('id')) {
                DB::table('member')->where('id_member', $request->query('id'))->update(['status' => 1]);
                return redirect()->route('dashboard', ['page' => 'kasir'])->with('success', 'Kasir berhasil diaktifkan.');
            }
            if ($request->has('deactivate') && $request->has('id')) {
                DB::table('member')->where('id_member', $request->query('id'))->update(['status' => 0]);
                return redirect()->route('dashboard', ['page' => 'kasir'])->with('success', 'Kasir berhasil dinonaktifkan.');
            }
            if ($request->has('hapus') && $request->has('id')) {
                $id = $request->query('id');
                DB::table('login')->where('id_member', $id)->delete();
                DB::table('nota')->where('id_member', $id)->delete();
                DB::table('penjualan')->where('id_member', $id)->delete();
                DB::table('member')->where('id_member', $id)->delete();
                return redirect()->route('dashboard', ['page' => 'kasir'])->with('success', 'Kasir berhasil dihapus.');
            }
        } elseif ($page === 'kasir/edit') {
            if ($request->isMethod('post')) {
                if ($request->has('update')) {
                    $id = $request->input('id');
                    $nama = $request->input('nama');
                    $alamat = $request->input('alamat');
                    $telepon = $request->input('telepon');
                    $email = $request->input('email');
                    $nik = $request->input('nik');
                    $role = $request->input('role');
                    
                    $updateData = [
                        'nm_member' => $nama,
                        'alamat_member' => $alamat,
                        'telepon' => $telepon,
                        'email' => $email,
                        'NIK' => $nik,
                        'role' => $role
                    ];
                    
                    if ($request->hasFile('gambar')) {
                        $file = $request->file('gambar');
                        $nama_file = time() . '_' . $file->getClientOriginalName();
                        $file->move(public_path('assets/img/user'), $nama_file);
                        $updateData['gambar'] = $nama_file;
                    }
                    
                    DB::table('member')->where('id_member', $id)->update($updateData);
                    return redirect()->route('dashboard', ['page' => 'kasir'])->with('success', 'Data Kasir berhasil diperbarui.');
                }
                
                if ($request->has('update_login')) {
                    $id = $request->input('id');
                    $user = $request->input('user');
                    $pass = $request->input('pass');
                    
                    $updateData = ['user' => $user];
                    if (!empty($pass)) {
                        $updateData['pass'] = md5($pass);
                    }
                    
                    DB::table('login')->where('id_member', $id)->update($updateData);
                    return redirect()->route('dashboard', ['page' => 'kasir'])->with('success', 'Data Login Kasir berhasil diperbarui.');
                }
            }
        }

        // Fetch Toko details globally for layout header
        $toko = DB::table('toko')->first();
        if (!$toko) {
            $toko = (object)[
                'nama_toko' => 'SmartKasir POS',
                'alamat_toko' => 'Indonesia',
                'tlp' => '0812-3456-7890',
                'nama_pemilik' => 'Owner SmartKasir'
            ];
        }

        $data = [
            'toko' => $toko,
            'page' => $page,
        ];

        if (empty($page)) {
            // Main Dashboard Stats
            $data['jml_barang'] = DB::table('barang')->count();
            $data['stok'] = (int) DB::table('barang')->sum('stok');
            $data['jual'] = (int) DB::table('nota')->sum('jumlah');
            $data['jml_kategori'] = DB::table('kategori')->count();
            
            // Low stock warning alerts
            $data['lowStockCount'] = DB::table('barang')->whereRaw('CAST(stok AS SIGNED) <= 3')->count();
            $data['lowStockList'] = DB::table('barang')->whereRaw('CAST(stok AS SIGNED) <= 3')->get();
        } 
        elseif ($page === 'barang') {
            $q = DB::table('barang')
                ->leftJoin('kategori', 'barang.id_kategori', '=', 'kategori.id_kategori')
                ->select('barang.*', 'kategori.nama_kategori');
            if ($request->query('stok') === 'yes') {
                $q->whereRaw('CAST(stok AS SIGNED) <= 3');
            }
            $data['barang'] = $q->get();
            $data['kategori'] = DB::table('kategori')->get();
        } 
        elseif ($page === 'barang/details') {
            $id = $request->query('barang');
            $data['barang'] = DB::table('barang')
                ->leftJoin('kategori', 'barang.id_kategori', '=', 'kategori.id_kategori')
                ->where('barang.id_barang', $id)
                ->select('barang.*', 'kategori.nama_kategori')
                ->first();
        }
        elseif ($page === 'barang/edit') {
            $id = $request->query('barang');
            $data['barang'] = DB::table('barang')
                ->leftJoin('kategori', 'barang.id_kategori', '=', 'kategori.id_kategori')
                ->where('barang.id_barang', $id)
                ->select('barang.*', 'kategori.nama_kategori', 'kategori.id_kategori')
                ->first();
            $data['kategori'] = DB::table('kategori')->get();
        }
        elseif ($page === 'kategori') {
            $data['kategori'] = DB::table('kategori')->get();
        } 
        elseif ($page === 'promo') {
            $data['promo'] = DB::table('promo')
                ->leftJoin('barang', 'promo.id_barang', '=', 'barang.id_barang')
                ->select('promo.*', 'barang.nama_barang')
                ->get();
            $data['barang'] = DB::table('barang')->get();
        }
        elseif ($page === 'promo/edit') {
            $id = $request->query('id');
            $data['promo'] = DB::table('promo')->where('id_promo', $id)->first();
            $data['barang'] = DB::table('barang')->get();
        }
        elseif ($page === 'jual') {
            // Point of Sales system data
            $data['barang'] = DB::table('barang')->get();
            // Active transaction items (not yet finalized)
            $data['penjualan'] = DB::table('penjualan')
                ->leftJoin('barang', 'penjualan.id_barang', '=', 'barang.id_barang')
                ->select('penjualan.*', 'barang.nama_barang', 'barang.merk')
                ->get();
        } 
        elseif ($page === 'laporan') {
            $data['laporan'] = DB::table('nota')
                ->leftJoin('barang', 'nota.id_barang', '=', 'barang.id_barang')
                ->select('nota.*', 'barang.nama_barang')
                ->get();
        } 
        elseif ($page === 'kasir') {
            $data['kasir'] = DB::table('member')
                ->leftJoin('login', 'member.id_member', '=', 'login.id_member')
                ->select('member.*', 'login.user')
                ->get();
        } 
        elseif ($page === 'kasir/details') {
            $id = $request->query('id');
            $data['kasir'] = DB::table('member')
                ->leftJoin('login', 'member.id_member', '=', 'login.id_member')
                ->where('member.id_member', $id)
                ->select('member.*', 'login.user')
                ->first();
        }
        elseif ($page === 'kasir/edit') {
            $id = $request->query('id');
            $data['kasir'] = DB::table('member')
                ->leftJoin('login', 'member.id_member', '=', 'login.id_member')
                ->where('member.id_member', $id)
                ->select('member.*', 'login.user')
                ->first();
        }
        elseif ($page === 'pengaturan') {
            $data['toko'] = $toko;
        } 
        elseif ($page === 'pesan') {
            $data['pesan'] = DB::table('pesan_kontak')->orderByDesc('id')->get();
        } 
        elseif ($page === 'user') {
            $id = session('admin')['id_member'];
            $data['user'] = DB::table('member')
                ->leftJoin('login', 'member.id_member', '=', 'login.id_member')
                ->where('member.id_member', $id)
                ->select('member.*', 'login.user')
                ->first();
        }

        return view('dashboard', $data);
    }

    public function logout()
    {
        Session::forget(['admin', 'role', 'status']);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['admin']);
        session_write_close();

        return redirect()->route('home');
    }

    public function registerForm()
    {
        return view('register');
    }

    public function registerStore(Request $request)
    {
        $data = $request->validate([
            'nm_member' => 'required|string|max:255',
            'alamat_member' => 'required|string',
            'telepon' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'user' => 'required|string|max:255',
            'pass' => 'required|string|min:4',
        ]);

        $memberId = DB::table('member')->insertGetId([
            'nm_member' => $data['nm_member'],
            'alamat_member' => $data['alamat_member'],
            'telepon' => $data['telepon'],
            'email' => $data['email'],
            'gambar' => '',
            'NIK' => '',
            'role' => 'kasir',
            'status' => 0,
        ]);

        DB::table('login')->insert([
            'user' => $data['user'],
            'pass' => md5($data['pass']),
            'id_member' => $memberId,
        ]);

        Session::flash('success', 'Pendaftaran berhasil. Silakan hubungi admin untuk aktifasi akun.');

        return redirect()->route('login');
    }
}
