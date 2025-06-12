document.addEventListener('DOMContentLoaded', () => {

    const HEADERS = {'Content-Type': 'application/json'};

    function initTomSelect(sel) {
        const apiBase = sel.dataset.api;
        const multi = sel.multiple || sel.dataset.mode === 'multi';
        const allowCreate = sel.dataset.create !== 'false';
        const currentId = sel.dataset.current || null;
        const selectedIds = (sel.dataset.selected || '')
            .split(',')
            .map(s => parseInt(s, 10))
            .filter(Boolean);

        async function api(path, opts = {}) {
            const r = await fetch(`${apiBase}${path}`, {...opts, headers: HEADERS});
            const j = await r.json();
            if (!j.success) throw j;
            return j.data;
        }

        const ts = new TomSelect(sel, {
            plugins: multi ? ['remove_button'] : [],
            preload: true,
            maxItems: multi ? null : 1,
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            placeholder: multi ? 'Виберіть або створіть…' : 'Почніть вводити…',
            persist: false,
            createFilter: v => allowCreate && v.length >= 2,
            loadThrottle: 300,

            load(q, cb) {
                api(`/list?query=${encodeURIComponent(q)}`)
                    .then(cb)
                    .catch(() => cb());
            },

            create: allowCreate ? async function (input, cb) {
                try {
                    const {id} = await api('/create', {
                        method: 'POST',
                        body: JSON.stringify({name: input})
                    });
                    cb({id, name: input});
                    this.addItem(id, false);          // миттєво вибрати
                } catch (e) {
                    alert(e.errors?.name?.[0] ?? 'Помилка створення');
                    cb(null);
                }
            } : false,

            onFocus() {
                if (!this.control_input.value && !this.options.length) this.load('');
            }
        });

        if (!multi && currentId) {              // SINGLE
            api(`/get?id=${currentId}`)
                .then(({id, name}) => {
                    ts.addOption({id, name});
                    ts.setValue(id);
                })
                .catch(console.error);
        }

        if (multi && selectedIds.length) {      // MULTI
            Promise.all(selectedIds.map(id => api(`/get?id=${id}`)))
                .then(arr => {
                    arr.forEach(({id, name}) => {
                        ts.addOption({id, name});
                        ts.addItem(id, false);
                    });
                })
                .catch(console.error);
        }
    }

    document.querySelectorAll('select[data-api]').forEach(initTomSelect);
});