(function () {
  'use strict';

  const NGUONG = 2 / 3;
  const THU_TU = ['hy', 'no', 'ai', 'o']; // cũng là thứ tự ưu tiên khi hòa điểm
  const NHAN = { hy: 'Hỷ', no: 'Nộ', ai: 'Ái', o: 'Ố' };
  const BINH_THUONG = 'bt'; // thư mục ảnh "bình thường" (mặt điềm tĩnh): không có điểm, không lên biểu đồ
  // Ngưỡng biểu lộ: điểm tối thiểu để một cảm xúc được hiện ra mặt. Chương nào cần khác thì
  // khai báo noi_dung.nguong_bieu_lo trong JSON để ghi đè. (Số dưới đây là giá trị mẫu.)
  const NGUONG_BIEU_LO = { hy: 17, no: 29, ai: 23, o: 30 };

  const g = document.getElementById('game');
  const tk = document.getElementById('tong-ket');
  if (!g) return;
  if (!tk) { console.error('Thiếu khối #tong-ket trong chuong.blade.php'); return; }

  const $ = id => document.getElementById(id);

  const el = {
    ten: $('ten-nv'),
    thoai: $('loi-thoai'),
    chon: $('lua-chon'),
    anh: $('anh-nv'),
    khung: $('khung-thoai'),   // khung thoại, bấm để sang câu kế
    goiY: $('goi-y'),
    nx: $('gm-nx'),
    bieuDo: $('bieu-do'),
    tienTrinh: $('tien-trinh'),
  };

  // i = giai đoạn, j = cột mốc, k = câu thoại
  const st = {
    diem: null, thuMuc: '', gd: [], i: 0, j: 0, k: 0,
    cotMoc: [],           // danh sách phẳng mọi cột mốc, dùng cho thanh tiến trình
    nguong: Object.assign({}, NGUONG_BIEU_LO), // ngưỡng biểu lộ của chương đang chơi
    dieuKien: {}, chuong: { thu_tu: 1, tieu_de: '' },
    anh: '',              // ảnh đang hiển thị
    anhDaGap: new Set(),  // các ảnh đã xuất hiện trong chương
    soLanDoi: 0,          // số lần ảnh đổi ở cột mốc lớn
    vatPham: [],          // sẽ nối ở bước game-shop
  };

  /* ---------- Chọn ảnh (bản JS của App\Support\ChonAnh) ---------- */
  function chonAnh(diem, thuMuc, nguong) {
    // Chỉ những cảm xúc đã đạt ngưỡng biểu lộ của chính nó mới được xét
    const du = THU_TU
      .map(k => [k, Math.max(0, diem[k] || 0)])
      .filter(([k, v]) => v > 0 && v >= nguong[k])
      .sort((a, b) => b[1] - a[1]); // hòa điểm thì giữ thứ tự hỷ, nộ, ái, ố

    // Chưa cảm xúc nào đạt ngưỡng: giữ mặt điềm tĩnh
    if (!du.length) return `characters/${thuMuc}/${BINH_THUONG}/${BINH_THUONG}.png`;

    const [chinh, diemChinh] = du[0];
    const phu = du[1]; // cảm xúc phụ cũng phải đạt ngưỡng của nó
    const ketHop = !!phu && phu[1] > diemChinh * NGUONG;
    const file = ketHop ? `${chinh}_${phu[0]}.png` : `${chinh}.png`;
    return `characters/${thuMuc}/${chinh}/${file}`;
  }

  /* ---------- Hiển thị trong màn chơi ---------- */
  function baoThieuAnh(duongDan) {
    el.anh.onerror = null;
    el.anh.classList.add('thieu');
    el.anh.alt = 'Thiếu ảnh: ' + duongDan;
  }

  function hienAnh(duongDan) {
    const goc = g.dataset.anhGoc + '/';
    el.anh.classList.remove('thieu');
    el.anh.alt = 'Nhân vật';

    el.anh.onerror = function () {
      // Dự phòng: về ảnh thuần khiết của cảm xúc chính (hy_ai.png -> hy.png)
      const p = duongDan.split('/');
      const thuMucCamXuc = p[p.length - 2];
      const duPhong = p.slice(0, -1).join('/') + '/' + thuMucCamXuc + '.png';
      if (duPhong === duongDan) return baoThieuAnh(duongDan);
      el.anh.onerror = () => baoThieuAnh(duPhong);
      el.anh.src = goc + duPhong;
    };
    el.anh.src = goc + duongDan;
  }

  function dungBieuDo() {
    el.bieuDo.innerHTML =
      '<div class="gm-bars">' + THU_TU.map(k =>
        `<div class="gm-col"><span id="gm-v-${k}">0</span><i class="gm-b gm-b-${k}" id="gm-b-${k}"></i></div>`
      ).join('') + '</div>' +
      '<div class="gm-labels">' + THU_TU.map(k => `<span>${NHAN[k]}</span>`).join('') + '</div>';
  }

  function capNhatDiem() {
    const max = Math.max(20, ...THU_TU.map(k => st.diem[k]));
    THU_TU.forEach(k => {
      $('gm-v-' + k).textContent = st.diem[k];
      $('gm-b-' + k).style.height = (st.diem[k] / max * 80) + '%';
    });
  }

  /* ---------- Thanh tiến trình (sao = cột mốc lớn, chấm = cột mốc nhỏ) ---------- */
  const SAO = '<svg class="gm-star" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 0C13 8 16 11 24 12 16 13 13 16 12 24 11 16 8 13 0 12 8 11 11 8 12 0Z"/></svg>';

  function dungTienTrinh() {
    const n = st.cotMoc.length;
    if (!n) return;
    el.tienTrinh.innerHTML =
      '<div class="gm-rail"><i class="gm-fill" id="gm-fill"></i></div><div class="gm-pts">' +
      st.cotMoc.map((m, i) => {
        const lon = m.loai === 'lon';
        const trai = n > 1 ? i / (n - 1) * 100 : 0;
        return `<span class="gm-pt ${lon ? 'big' : 'sm'}" style="left:${trai}%">${lon ? SAO : ''}</span>`;
      }).join('') + '</div>';
    el.tienTrinh.setAttribute('role', 'progressbar');
    el.tienTrinh.setAttribute('aria-valuemin', '0');
    el.tienTrinh.setAttribute('aria-valuemax', n);
  }

  function viTriHienTai() { // vị trí cột mốc hiện tại trong danh sách phẳng
    let s = 0;
    for (let a = 0; a < st.i && a < st.gd.length; a++) s += (st.gd[a].cot_moc || []).length;
    return s + st.j;
  }

  function capNhatTienTrinh(viTri) {
    const n = st.cotMoc.length;
    if (!n) return;
    el.tienTrinh.querySelectorAll('.gm-pt').forEach((p, i) => {
      p.classList.toggle('done', i < viTri);
      p.classList.toggle('cur', i === viTri && viTri < n);
    });
    $('gm-fill').style.width = (viTri >= n ? 100 : (n > 1 ? viTri / (n - 1) * 100 : 0)) + '%';
    el.tienTrinh.setAttribute('aria-valuenow', Math.min(viTri, n));
  }

  function congDiem(d) {
    for (const k in d) {
      if (k in st.diem) st.diem[k] = Math.max(0, st.diem[k] + d[k]); // chặn tối thiểu 0
    }
    capNhatDiem();
  }

  function tenHienThi(s) {
    return s ? s.charAt(0).toUpperCase() + s.slice(1) : '';
  }

  function taoNut(nhan, khiBam) {
    const b = document.createElement('button');
    b.type = 'button';
    b.textContent = nhan;
    b.addEventListener('click', khiBam);
    el.chon.appendChild(b);
    return b;
  }

  /* ---------- Luồng chạy ---------- */
  function sangCotMocKe() {
    st.k = 0;
    st.j++;
    const gd = st.gd[st.i];
    if (!gd || st.j >= gd.cot_moc.length) {
      st.i++;
      st.j = 0;
    }
  }

  function hienThoai(t) {
    el.ten.textContent = tenHienThi(t.nhan_vat);
    el.thoai.textContent = t.text || '';
    el.chon.innerHTML = '';

    const coChon = Array.isArray(t.lua_chon) && t.lua_chon.length;
    if (coChon) {
      t.lua_chon.forEach(lc => {
        taoNut(lc.text, () => {
          congDiem(lc.diem || {});
          st.k++;
          buoc();
        });
      });
    }
    // Có lựa chọn thì khóa khung thoại; không có thì bấm khung thoại để sang câu kế
    el.khung.disabled = !!coChon;
    el.goiY.hidden = !!coChon;
    el.nx.hidden = !!coChon;
    if (!coChon) el.khung.focus({ preventScroll: true });
  }

  function buoc() {
    const gd = st.gd[st.i];
    if (!gd) return hienKetThuc();

    const cm = (gd.cot_moc || [])[st.j];
    if (!cm) { // giai đoạn rỗng hoặc hết cột mốc
      st.i++; st.j = 0; st.k = 0;
      return buoc();
    }

    capNhatTienTrinh(viTriHienTai());

    if (cm.loai === 'lon') { // cột mốc lớn: chọn lại ảnh theo điểm hiện tại
      const anhMoi = chonAnh(st.diem, st.thuMuc, st.nguong);
      if (anhMoi !== st.anh) st.soLanDoi++;
      st.anh = anhMoi;
      st.anhDaGap.add(anhMoi);
      hienAnh(anhMoi);
      sangCotMocKe();
      return buoc();
    }

    const t = (cm.thoai || [])[st.k]; // cột mốc nhỏ: giữ nguyên ảnh
    if (!t) {
      sangCotMocKe();
      return buoc();
    }
    hienThoai(t);
  }

  /* ---------- Hết truyện: giữ nguyên màn chơi, chờ người chơi bấm Kết thúc ---------- */
  function hienKetThuc() {
    capNhatTienTrinh(st.cotMoc.length);
    el.chon.innerHTML = '';
    el.khung.disabled = true;
    el.goiY.hidden = true;
    el.nx.hidden = true;
    const nut = taoNut('Kết thúc', () => hienTongKet());
    nut.classList.add('gm-end');
    nut.focus({ preventScroll: true });
  }

  /* ---------- Màn tổng kết ---------- */
  function bao(text) {
    const t = $('tk-toast');
    t.textContent = text;
    t.classList.add('show');
    clearTimeout(bao.h);
    bao.h = setTimeout(() => t.classList.remove('show'), 2200);
  }

  function hienTongKet() {
    const dat = Object.keys(st.dieuKien).every(k => (st.diem[k] || 0) >= st.dieuKien[k]);
    const max = Math.max(1, ...THU_TU.map(k => st.diem[k]));

    g.hidden = true;
    tk.hidden = false;
    document.body.classList.add('tk-mode');
    window.scrollTo(0, 0);

    $('tk-chuong').textContent = 'Chương ' + st.chuong.thu_tu + ': ' + st.chuong.tieu_de;

    // Biểu đồ cột
    const chart = $('tk-chart');
    const nhan = $('tk-labels');
    chart.innerHTML = '';
    nhan.innerHTML = '';
    THU_TU.forEach(k => {
      const cot = document.createElement('div');
      cot.className = 'tk-col';
      const so = document.createElement('span');
      so.className = 'tk-val';
      so.textContent = st.diem[k];
      const bar = document.createElement('div');
      bar.className = 'tk-bar tk-c-' + k;
      bar.dataset.h = Math.round((st.diem[k] / max) * 190);
      cot.append(so, bar);
      chart.appendChild(cot);

      const ten = document.createElement('span');
      ten.textContent = NHAN[k];
      nhan.appendChild(ten);
    });
    void chart.offsetHeight; // ép trình duyệt tính layout để hiệu ứng cột chạy
    requestAnimationFrame(() => {
      chart.querySelectorAll('.tk-bar').forEach(b => {
        b.style.height = Math.max(4, +b.dataset.h) + 'px';
      });
    });

    // Các dòng thống kê
    $('tk-doi').textContent = st.soLanDoi;
    $('tk-gap').textContent = st.anhDaGap.size;

    const vp = $('tk-vp');
    vp.innerHTML = '';
    if (st.vatPham.length) {
      st.vatPham.forEach(ten => {
        const s = document.createElement('span');
        s.className = 'tk-tag';
        s.textContent = ten;
        vp.appendChild(s);
      });
    } else {
      const s = document.createElement('span');
      s.className = 'tk-none';
      s.textContent = 'Chưa có';
      vp.appendChild(s);
    }

    const dk = Object.entries(st.dieuKien).map(([k, v]) => NHAN[k] + ' ≥ ' + v);
    $('tk-need').textContent = dk.length ? 'Cần ' + dk.join(', ') : 'Không có điều kiện';

    const kq = $('tk-kq');
    kq.textContent = dat ? 'Đạt' : 'Chưa đạt';
    kq.className = 'tk-result ' + (dat ? 'pass' : 'fail');

    $('tk-next').disabled = !dat;
  }

  function noiNut() {
    // Màn chơi: khung thoại và menu
    el.khung.addEventListener('click', () => {
      if (el.khung.disabled) return;
      st.k++;
      buoc();
    });
    const menu = $('menu-ov');
    const dongMenu = () => menu.classList.remove('show');
    $('menu-btn').addEventListener('click', () => { menu.classList.add('show'); $('menu-tiep').focus(); });
    $('menu-tiep').addEventListener('click', dongMenu);
    $('menu-lai').addEventListener('click', () => location.reload()); // vào lại = đặt lại điểm khởi đầu
    menu.addEventListener('click', e => { if (e.target === menu) dongMenu(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') dongMenu(); });

    // Màn tổng kết
    $('tk-back').addEventListener('click', () => { location.href = tk.dataset.dsChuong || tk.dataset.thoat; });
    $('tk-thoat').addEventListener('click', () => { location.href = tk.dataset.thoat; });
    $('tk-again').addEventListener('click', () => location.reload()); // vào lại = đặt lại điểm khởi đầu
    $('tk-next').addEventListener('click', () => bao('Chương tiếp theo sẽ nối ở bước 5.'));
    $('tk-menu').addEventListener('click', () => bao('Menu game sẽ làm sau.'));
  }

  /* ---------- Khởi động ---------- */
  async function khoiDong() {
    noiNut();
    dungBieuDo();
    try {
      const res = await fetch(g.dataset.vao, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
          'Accept': 'application/json',
        },
      });
      if (!res.ok) {
        el.thoai.textContent = res.status === 403
          ? 'Chương này đang bị khóa.'
          : 'Không vào được chương (lỗi ' + res.status + ').';
        return;
      }
      const d = await res.json();
      st.diem = Object.assign({ hy: 0, no: 0, ai: 0, o: 0 }, d.diem);
      st.thuMuc = d.thu_muc;
      st.chuong = d.chuong;
      st.gd = (d.noi_dung && d.noi_dung.giai_doan) || [];
      st.dieuKien = (d.noi_dung && d.noi_dung.dieu_kien) || {};
      // Ngưỡng và ảnh khởi đầu do server quyết định (App\Support\ChonAnh); thiếu thì tự tính
      st.nguong = Object.assign({}, NGUONG_BIEU_LO, d.nguong || (d.noi_dung && d.noi_dung.nguong_bieu_lo) || {});
      st.anh = d.anh || chonAnh(st.diem, st.thuMuc, st.nguong);
      st.anhDaGap = new Set([st.anh]);
      st.cotMoc = st.gd.flatMap(gd => gd.cot_moc || []);
      dungTienTrinh();
      capNhatDiem();
      hienAnh(st.anh);
      buoc();
    } catch (e) {
      el.thoai.textContent = 'Lỗi kết nối, hãy tải lại trang.';
    }
  }

  khoiDong();
})();