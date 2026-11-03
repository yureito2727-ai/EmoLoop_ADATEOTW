(function () {
  'use strict';

  const NGUONG = 2 / 3;
  const THU_TU = ['hy', 'no', 'ai', 'o']; // cũng là thứ tự ưu tiên khi hòa điểm
  const NHAN = { hy: 'Hỷ', no: 'Nộ', ai: 'Ái', o: 'Ố' };

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
    d: { hy: $('d-hy'), no: $('d-no'), ai: $('d-ai'), o: $('d-o') },
  };

  // i = giai đoạn, j = cột mốc, k = câu thoại
  const st = {
    diem: null, thuMuc: '', gd: [], i: 0, j: 0, k: 0,
    dieuKien: {}, chuong: { thu_tu: 1, tieu_de: '' },
    anh: '',              // ảnh đang hiển thị
    anhDaGap: new Set(),  // các ảnh đã xuất hiện trong chương
    soLanDoi: 0,          // số lần ảnh đổi ở cột mốc lớn
    vatPham: [],          // sẽ nối ở bước game-shop
  };

  /* ---------- Chọn ảnh (bản JS của App\Support\ChonAnh) ---------- */
  function chonAnh(diem, thuMuc) {
    const xep = THU_TU
      .map(k => [k, Math.max(0, diem[k] || 0)])
      .sort((a, b) => b[1] - a[1]);
    const [chinh, diemChinh] = xep[0];
    const [phu, diemPhu] = xep[1];
    const ketHop = diemPhu > diemChinh * NGUONG;
    const file = ketHop ? `${chinh}_${phu}.png` : `${chinh}.png`;
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

  function capNhatDiem() {
    THU_TU.forEach(k => { el.d[k].textContent = st.diem[k]; });
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

    if (Array.isArray(t.lua_chon) && t.lua_chon.length) {
      t.lua_chon.forEach(lc => {
        taoNut(lc.text, () => {
          congDiem(lc.diem || {});
          st.k++;
          buoc();
        });
      });
    } else {
      taoNut('Tiếp', () => {
        st.k++;
        buoc();
      });
    }
  }

  function buoc() {
    const gd = st.gd[st.i];
    if (!gd) return hienTongKet();

    const cm = (gd.cot_moc || [])[st.j];
    if (!cm) { // giai đoạn rỗng hoặc hết cột mốc
      st.i++; st.j = 0; st.k = 0;
      return buoc();
    }

    if (cm.loai === 'lon') { // cột mốc lớn: chọn lại ảnh theo điểm hiện tại
      const anhMoi = chonAnh(st.diem, st.thuMuc);
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
    const thoat = () => { location.href = tk.dataset.thoat; };
    $('tk-back').addEventListener('click', thoat);
    $('tk-thoat').addEventListener('click', thoat);
    $('tk-again').addEventListener('click', () => location.reload()); // vào lại = đặt lại điểm khởi đầu
    $('tk-next').addEventListener('click', () => bao('Chương tiếp theo sẽ nối ở bước 5.'));
    $('tk-menu').addEventListener('click', () => bao('Menu game sẽ làm sau.'));
  }

  /* ---------- Khởi động ---------- */
  async function khoiDong() {
    noiNut();
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
      st.anh = d.anh;
      st.anhDaGap = new Set([d.anh]);
      capNhatDiem();
      hienAnh(d.anh);
      buoc();
    } catch (e) {
      el.thoai.textContent = 'Lỗi kết nối, hãy tải lại trang.';
    }
  }

  khoiDong();
})();