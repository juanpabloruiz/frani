'use strict';

const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');

const fuente = fs.readFileSync(path.join(__dirname, '../src/js/compartir-producto.js'), 'utf8');
const parcial = fs.readFileSync(path.join(__dirname, '../src/_compartir-producto.php'), 'utf8');
const urlProducto = 'https://frani.ar/general/vaso-stitch-celeste-con-sorbete';
const tituloProducto = 'Vaso Stitch celeste con sorbete | Frani';
const rutaImagen = '/imagen-estado.php?v=prueba&producto=510';
const nombreImagen = 'frani-estado-510.jpg';

class Elemento {
    constructor({hidden = false, dataset = {}} = {}) {
        this.hidden = hidden;
        this.dataset = dataset;
        this.disabled = false;
        this.textContent = '';
        this.open = false;
        this.eventos = new Map();
        this.hijos = new Map();
        this.vecesModal = 0;
    }

    querySelector(selector) { return this.hijos.get(selector) || null; }
    addEventListener(nombre, funcion) { this.eventos.set(nombre, funcion); }
    showModal() { this.open = true; this.vecesModal++; }
    focus() { this.enfocado = true; }
    select() { this.seleccionado = true; }
    async click() {
        const evento = {preventDefault() { this.defaultPrevented = true; }};
        const funcion = this.eventos.get('click');
        if (funcion) await funcion(evento);
        return evento;
    }
}

class Archivo {
    constructor(partes, nombre, opciones) {
        this.name = nombre;
        this.type = opciones.type;
        this.size = partes.reduce((total, parte) => total + parte.size, 0);
    }
}

function diferido() {
    let resolve, reject;
    const promise = new Promise((ok, error) => { resolve = ok; reject = error; });
    return {promise, resolve, reject};
}

function respuesta({ok = true, tipo = 'image/jpeg', size = 48000} = {}) {
    return {ok, headers: {get: (nombre) => nombre === 'Content-Type' ? tipo : null},
        blob: async () => ({size, type: tipo})};
}

async function completarPromesas() {
    // fetch, blob y preparación se resuelven antes del próximo turno de UI.
    await new Promise((resolve) => setImmediate(resolve));
}

function escenario(opciones = {}) {
    const compartir = new Elemento({dataset: {title: tituloProducto, url: urlProducto}});
    const selectores = ['copy-link', 'native-share', 'share-status', 'share-fallback',
        'state-preview', 'state-dialog', 'state-share', 'state-status', 'state-download'];
    const elementos = {};
    for (const selector of selectores) {
        const elemento = new Elemento({hidden: ['copy-link', 'native-share', 'share-status',
            'share-fallback', 'state-share'].includes(selector)});
        elementos[selector] = elemento;
        compartir.hijos.set(`[data-${selector}]`, elemento);
    }
    const input = new Elemento();
    elementos['share-fallback'].hijos.set('input', input);
    elementos['state-dialog'].dataset = {image: rutaImagen, filename: nombreImagen};
    elementos['state-download'].href = rutaImagen + '&descargar=1';
    if (opciones.sinDialogo) elementos['state-dialog'].showModal = undefined;
    const peticiones = [], compartidos = [], capacidades = [];
    const navigator = {
        share: async (datos) => {
            compartidos.push(datos);
            if (opciones.share) return opciones.share(datos);
        },
        canShare: (datos) => {
            capacidades.push(datos);
            return opciones.canShare ? opciones.canShare(datos) : true;
        },
    };
    if (opciones.sinShare) delete navigator.share;
    if (opciones.sinCanShare) delete navigator.canShare;
    const contexto = {
        document: {querySelector: () => opciones.sinProducto ? null : compartir},
        window: {isSecureContext: opciones.seguro !== false},
        navigator,
        File: opciones.sinFile ? undefined : Archivo,
        fetch: (url, configuracion) => {
            peticiones.push({url, configuracion});
            return opciones.fetch ? opciones.fetch(url, configuracion) : Promise.resolve(respuesta());
        },
    };
    vm.runInNewContext(fuente, contexto, {filename: 'compartir-producto.js'});
    return {elementos, input, peticiones, compartidos, capacidades};
}

function soloArchivo(datos) {
    assert.deepEqual(Object.keys(datos), ['files'], 'La imagen no adjunta URL, título ni texto');
    assert.equal(datos.files.length, 1);
    const archivo = datos.files[0];
    assert.ok(archivo instanceof Archivo);
    assert.equal(archivo.type, 'image/jpeg');
    assert.equal(archivo.name, nombreImagen);
    assert.ok(archivo.size > 0);
    return archivo;
}

const casos = [];
function probar(nombre, funcion) { casos.push({nombre, funcion}); }

probar('abre la vista previa y prepara el JPEG antes de habilitar compartir', async () => {
    const descarga = diferido();
    const s = escenario({fetch: () => descarga.promise});
    assert.equal(s.elementos['state-share'].hidden, true);
    const evento = await s.elementos['state-preview'].click();
    assert.equal(evento.defaultPrevented, true);
    assert.equal(s.elementos['state-dialog'].open, true);
    assert.equal(s.compartidos.length, 0, 'Abrir la vista previa no invoca el selector de apps');
    assert.equal(s.elementos['state-share'].hidden, true, 'El archivo pendiente no se puede compartir');
    assert.equal(s.peticiones.length, 1);
    assert.equal(s.peticiones[0].url, rutaImagen, 'Descarga la imagen del mismo origen');
    assert.equal(s.peticiones[0].configuracion.credentials, 'omit');

    await s.elementos['state-preview'].click();
    assert.equal(s.peticiones.length, 1, 'Reabrir durante la preparación no duplica la descarga');
    descarga.resolve(respuesta({tipo: 'image/jpeg; charset=binary'}));
    await completarPromesas();
    assert.equal(s.elementos['state-share'].hidden, false);
    soloArchivo(s.capacidades[0]);

    await s.elementos['state-share'].click();
    assert.equal(s.compartidos.length, 1);
    const archivo = soloArchivo(s.compartidos[0]);
    assert.equal(archivo, s.capacidades[0].files[0], 'Comparte el archivo ya preparado');
    assert.equal(s.peticiones.length, 1, 'El clic de compartir no espera otra descarga');
    assert.equal(s.elementos['state-share'].disabled, false);
    s.elementos['state-dialog'].open = false;
    await s.elementos['state-preview'].click();
    assert.equal(s.peticiones.length, 1, 'Reabrir conserva el archivo preparado');
    assert.equal(s.elementos['state-dialog'].vecesModal, 2);
});

probar('no comparte si se pulsa antes de que el archivo esté listo', async () => {
    const descarga = diferido();
    const s = escenario({fetch: () => descarga.promise});
    await s.elementos['state-preview'].click();
    await s.elementos['state-share'].click();
    assert.equal(s.compartidos.length, 0);
    descarga.resolve(respuesta());
    await completarPromesas();
});

probar('cancelar el selector conserva el mensaje y vuelve a habilitar el botón', async () => {
    const seleccion = diferido();
    const s = escenario({share: () => seleccion.promise});
    await s.elementos['state-preview'].click();
    await completarPromesas();
    const mensaje = s.elementos['state-status'].textContent;
    const clic = s.elementos['state-share'].click();
    assert.equal(s.elementos['state-share'].disabled, true);
    seleccion.reject(Object.assign(new Error('Cancelado'), {name: 'AbortError'}));
    await clic;
    assert.equal(s.elementos['state-share'].disabled, false);
    assert.equal(s.elementos['state-status'].textContent, mensaje);
});

probar('un rechazo al compartir conserva la alternativa de descargar', async () => {
    const s = escenario({share: async () => {
        throw Object.assign(new Error('Sin permiso'), {name: 'NotAllowedError'});
    }});
    await s.elementos['state-preview'].click();
    await completarPromesas();
    await s.elementos['state-share'].click();
    soloArchivo(s.compartidos[0]);
    assert.equal(s.elementos['state-share'].disabled, false);
    assert.match(s.elementos['state-status'].textContent, /descargar.*Mi estado.*galería/i);
    assert.equal(s.elementos['state-download'].hidden, false);
});

for (const [nombre, opciones] of [
    ['sin canShare', {sinCanShare: true}],
    ['sin share', {sinShare: true}],
    ['sin File', {sinFile: true}],
    ['contexto inseguro', {seguro: false}],
]) {
    probar(`${nombre}: muestra vista previa y descarga sin solicitar el archivo para compartir`, async () => {
        const s = escenario(opciones);
        const href = s.elementos['state-download'].href;
        await s.elementos['state-preview'].click();
        await completarPromesas();
        assert.equal(s.elementos['state-dialog'].open, true);
        assert.equal(s.peticiones.length, 0);
        assert.equal(s.compartidos.length, 0);
        assert.equal(s.elementos['state-share'].hidden, true);
        assert.equal(s.elementos['state-download'].hidden, false);
        assert.equal(s.elementos['state-download'].href, href);
    });
}

probar('canShare false mantiene la descarga y oculta el botón nativo de archivo', async () => {
    const s = escenario({canShare: () => false});
    await s.elementos['state-preview'].click();
    await completarPromesas();
    assert.equal(s.peticiones.length, 1);
    assert.equal(s.capacidades.length, 1);
    assert.equal(s.elementos['state-share'].hidden, true);
    assert.match(s.elementos['state-status'].textContent, /galería.*Mi estado/i);
    assert.equal(s.elementos['state-download'].hidden, false);
    await s.elementos['state-share'].click();
    assert.equal(s.compartidos.length, 0);
});

for (const [nombre, fetch] of [
    ['HTTP no exitoso', async () => respuesta({ok: false})],
    ['contenido no JPEG', async () => respuesta({tipo: 'text/html'})],
    ['Content-Type ausente', async () => respuesta({tipo: null})],
    ['imagen vacía', async () => respuesta({size: 0})],
    ['error de red', async () => { throw new Error('Sin conexión'); }],
]) {
    probar(`${nombre}: ofrece descargar y no comparte un archivo inválido`, async () => {
        const s = escenario({fetch});
        await s.elementos['state-preview'].click();
        await completarPromesas();
        assert.equal(s.elementos['state-share'].hidden, true);
        assert.match(s.elementos['state-status'].textContent, /descargar.*Mi estado.*galería/i);
        assert.equal(s.elementos['state-download'].hidden, false);
        assert.equal(s.capacidades.length, 0);
        await s.elementos['state-share'].click();
        assert.equal(s.compartidos.length, 0);
    });
}

probar('compartir enlace sigue enviando título y URL canónica sin archivo', async () => {
    const s = escenario();
    assert.equal(s.elementos['native-share'].hidden, false);
    await s.elementos['native-share'].click();
    assert.equal(s.peticiones.length, 0);
    assert.equal(s.compartidos.length, 1);
    assert.deepEqual(Object.keys(s.compartidos[0]).sort(), ['title', 'url']);
    assert.equal(s.compartidos[0].title, tituloProducto);
    assert.equal(s.compartidos[0].url, urlProducto);
    assert.equal(s.elementos['native-share'].disabled, false);
});

probar('sin dialog nativo el enlace a la imagen sigue funcionando', async () => {
    const s = escenario({sinDialogo: true});
    const evento = await s.elementos['state-preview'].click();
    assert.equal(evento.defaultPrevented, undefined);
    assert.equal(s.peticiones.length, 0);
    await s.elementos['native-share'].click();
    assert.equal(s.compartidos[0].url, urlProducto);
});

probar('HTML inicial mantiene descarga y oculta controles que necesitan JavaScript', () => {
    const botonImagen = parcial.match(/<button\b[^>]*data-state-share[^>]*>/);
    assert.ok(botonImagen, 'Existe el control de compartir imagen');
    assert.match(botonImagen[0], /\bhidden\b/);
    const etiquetasEnlace = parcial.match(/<a\b(?:"[^"]*"|'[^']*'|[^'">])*>/g) || [];
    const descarga = etiquetasEnlace.find((etiqueta) => etiqueta.includes('data-state-download'));
    assert.ok(descarga, 'La descarga existe incluso sin API de compartir');
    assert.match(descarga, /href=/);
    assert.match(descarga, /download=/);
    assert.doesNotMatch(descarga, /\bhidden\b/);
    assert.ok(etiquetasEnlace.some((etiqueta) => etiqueta.includes('data-state-preview')));
});

probar('puede cargarse en una página que no tenga producto', () => {
    assert.doesNotThrow(() => escenario({sinProducto: true}));
});

(async () => {
    for (const {nombre, funcion} of casos) {
        await funcion();
        process.stdout.write(`OK: ${nombre}\n`);
    }
    process.stdout.write(`${casos.length} pruebas de compartir producto completadas.\n`);
})().catch((error) => {
    process.stderr.write(`${error.stack}\n`);
    process.exitCode = 1;
});
