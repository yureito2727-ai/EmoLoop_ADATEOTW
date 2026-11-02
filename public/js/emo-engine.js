(function () {
  'use strict';

  const NGUONG = 2 / 3;
  const THU_TU = ['hy', 'no', 'ai', 'o']; // thứ tự này cũng là thứ tự ưu tiên khi hòa điểm

  const g = document.getElementById('game');
  if (!g) return;

  const el = {
    ten: document.getElementById('ten-nv'),
    thoai: document.getElementById('loi-thoai'),
    chon: document.getElementById('lua-chon'),
    anh: document.getElementById('anh-nv'),
    d: {
      hy: document.getElementById('d-hy'),
      no: document.getElementById('d-no'),
      ai: document.getElementById('d-ai'),
      o: document.getElementById('d-o'),
    },
  };

  // Trạng thái: i = giai đoạn, j = cột mốc, k = câu thoại
  const st = { diem: null, thuMuc: '', gd: [], i: 0, j: 0, k: 0 };

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

  /* ---------- Hiển thị ---------- */
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

  function ketThuc() {
    el.ten.textContent = '';
    el.thoai.textContent = 'Hết chương. (Màn tổng kết sẽ làm ở bước sau.)';
    el.chon.innerHTML = '';
  }

  function buoc() {
    const gd = st.gd[st.i];
    if (!gd) return ketThuc();

    const cm = (gd.cot_moc || [])[st.j];
    if (!cm) { // giai đoạn rỗng hoặc hết cột mốc
      st.i++; st.j = 0; st.k = 0;
      return buoc();
    }

    if (cm.loai === 'lon') { // cột mốc lớn: chọn lại ảnh theo điểm hiện tại
      hienAnh(chonAnh(st.diem, st.thuMuc));
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

  /* ---------- Khởi động ---------- */
  async function khoiDong() {
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
      st.gd = (d.noi_dung && d.noi_dung.giai_doan) || [];
      capNhatDiem();
      hienAnh(d.anh);
      buoc();
    } catch (e) {
      el.thoai.textContent = 'Lỗi kết nối, hãy tải lại trang.';
    }
  }

  khoiDong();
})();