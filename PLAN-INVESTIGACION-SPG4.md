# Plan de investigación: SPG4 / SPAST-HSP

**Estrategia de silenciamiento génico (silence-and-replace) + tSCS**
Fecha: septiembre 2026

---

## 0. Diagnóstico de la situación

Lo que ya tienen:

- Vector AAV9 con microRNA anti-SPAST + cDNA M1/M87 sanas (silence-and-replace).
- Eficacia demostrada en C448Y con tratamiento **al nacer**, previniendo la enfermedad.
- Etapa humana de tSCS en diseño, estudio propio e independiente.

Tres hechos que reencuadran el programa:

1. **El diseño "nacimiento → prevenir" es profiláctico.** La ejecución preclínica de referencia
   (Piermarini et al., *Mol Ther* 2025, PMID 41311060, ICV en crías de C448Y) es el mismo diseño.
   El paciente SPG4 se diagnostica típicamente entre los 30 y 50 años, con axones corticoespinales
   ya degenerados. **El dataset actual no establece eficacia terapéutica; establece eficacia
   profiláctica y valida el constructo.** Esa distinción es la línea completa de este plan.

2. **Silenciar sin reemplazar sería erróneo, y ustedes lo hicieron bien.** La haploinsuficiencia es
   componente central de SPG4 (~50% de actividad de spastin en heterocigotos). La C448Y no es
   dominante-negativa, y silenciar spastina endógena *empeora* los defectos de tráfico de lisosomas
   (OMIM 604277). El brazo *silence-only* es un control experimental obligatorio, no opcional
   (ver E1.3).

3. **tSCS no es el mismo tipo de programa que el génico, y no debe水流arse como un combination
   product.** Uno busca modificar la enfermedad en 5–8 años; el otro busca una indicación de
   dispositivo ahora. Ver Eje 6 y Eje 7.

---

## 1. Tesis de investigación

> Silence-and-replace detiene la progresión de SPG4 **cuando se administra después del inicio de la
> enfermedad**, a través de una vía de entrega que alcanza los axones corticoespinales en el adulto,
> y el beneficio funcional es cuantificable con marcadores que低着头 in translationalmente a
> ensayos clínicos de tamaño realista.

Tres tesis secundarias que sostienen la principal:

- **T1 (ventana):** existe una ventana terapéutica más allá de la etapa profiláctica, definida por
  un umbral de pérdida corticoespinal cuantificable.
- **T2 (vía):** la vía intratecal funciona en adulto, donde la vía ICV neonatal no es trasladable.
- **T3 (combinación racional):** el silenciamiento de la isoforma M1 no degrada la proteína mutante
  ya acumulada, por lo que un co-tratamiento dirs，完成 laGap está en elclearance proteico, no en
  el silenciamiento.

---

## 2. Eje 1 — Trasladabilidad a animales sintomáticos ⭐ CRÍTICO

**Este eje decide si el programa tiene valor de desarrollo o solo valor de publicación.**

### H1
> Silence-and-replace administrado en ratones C448Y **adultos y sintomáticos** atenúa la
> degeneración del tracto corticoespinal y preserva la función de marcha, en comparación con
> animales no tratados de la misma edad.

### E1.1 — Experimento de eficacia en sintomáticos
- **Modelo:** `SPAST-C448Y` (Qiang et al., *Hum Mol Genet* 2019, PMID 30520996). Fenotipo de inicio
  adulto, mayor penetrancia en machos.
- **Edades de tratamiento:** P30 (pre-sintomático tardío), P90 (sintomático temprano), P180
  (sintomático establecido).
- **Endpoint primario:** métricas de marcha en CatWalk (longitud del paso, anchura de la pisada,
  simetría) a P60, P120, P240.
- **Endpoint secundario cuantitativo:** recuento de axones CST y densidad de neurofilamento en
  el tracto lateral (anticuerpos SMI-32 / NF-H), atrofia de médula espinal, y Western blot de
  M1 mutante y M87 con anticuerpo específico de C448Y.
- **Control de confirmar la carga mutante:** genotipado y perfil de isoforma basal por edad.

### E1.2 — Diseño de brazos (crítico)
| Brazo | Contenido | Pregunta que responde |
|---|---|---|
| WT + AAV9-GFP | Vector vacío | ¿El vector tiene efecto? |
| C448Y sin tratar | — | Línea base de progresión |
| **C448Y + silence-only** | microRNA anti-SPAST, **sin** cDNA | **¿El silenciamiento sin reemplazo empeora? (haploinsuficiencia)** |
| C448Y + replace-only | cDNA M1/M87, sin silenciamiento | ¿La sobreexpresión aislada basta? |
| **C448Y + silence-and-replace** | Vector completo | Efecto del programa completo |

El brazo **silence-only** es el control científico más valioso del proyecto. Predice un empeoramiento
parcial del fenotipo respecto a no tratar. Si se confirma, es una demostración directa in vivo de la
haploinsuficiencia de SPG4 — publicable por sí mismo y justifica empíricamente el diseño de
reemplazo.

### E1.3 — taille de muestra y diseño
- **n ≥ 12 por grupo**, balanceados por sexo. C448Y es más penetrante en machos: analizar sexo como
  factor, no agrupar. Justificar el n con la variabilidad observada en marcha de la colonia propia.
- Aleatorización de jaula, cegado del evaluador de marcha y del neuropatólogo, aleatorización del
  orden de tinción y de adquisición de imágenes (evitar drift de microscopía).
- Pre-registro del diseño y del plan de análisis antes de abrir los grupos.

### E1.4 — Criterio de decisión
- **Si hay beneficio funcional y preservesión axonal en P180 → el programa pasa a IND
  preclínico.** Es el resultado que justifica años de trabajo regulatorio.
- **Si solo estabiliza sin recuperar función → redefinir el claim a "modificación de enfermedad"
  (estabilización), lo que sigue siendo clínica y regulatoriamente viable, pero exige endpoints
  distintos.**
- **Si no hay beneficio en sintomáticos → el programa solo sirve como prevention/pediatric. Eso
  requiere una población deolis_children, que para SPG4 con inicio adulto es inexistente. En ese
  caso, replantear.**

---

## 3. Eje 2 — Ventana terapéutica y dosimetría

### H2
> Existe una ventana mínima de tratamiento definida por un umbral de pérdida corticoespinal, y el
> beneficio es dosis-dependiente hasta un techo marcado por toxicidad de sobreexpresión de spastina.

### E2.1 — Mapeo de la ventana
- Tratar a P30 / P60 / P90 / P120 / P180 en la misma cohorte C448Y.
- Medir el umbral de pérdida axonal en la misma curva de edad en cohortes no tratadas (marcador
  de referencia).
- Entregable: **un diagrama ventana-tiempo vs. pérdida axonal** que defina el punto de no retorno.

### E2.2 — Rango de dosis
- Dosis intracerebroventricular: 1×10⁸ / 3×10⁸ / 1×10⁹ / 3×10⁹ vg por ratón.
- Vigilar: hepatotoxicidad, neurotoxicidad en ganglios dorsales (dosis-dependiente, bien documentada
  con AAV), respuesta inmune.
- Buscar un techo de toxicidad explícito: la sobreexpresión de spastina **es** neurotóxica. El
  objetivo es expresión fisiológica, no supranormal — el cassette debe reportarse y cuantificarse
  contra niveles WT en estado estacionario.

### E2.3 — Criterio de decisión
Definir la **dosis mínima eficaz** y la **edad mínima eficaz** como los dos números que gobiernan
el diseño del primer ensayo en humanos.

---

## 4. Eje 3 — Vía de entrega en adulto ⭐ CRÍTICO

### H3
> La vía que funcionó en neonato no alcanza los axones corticoespinales en el adulto, y la vía
> intratecal sí lo hace con una dosisIntratecal trasladable.

### E3.1 — El problema
La transducción sistémica de AAV9 es **dependiente de edad** en roedor (la capacidad de cruzar la
barrera hematoencefálica cae drásticamente en adulto; PMID 17898796). La ejecución de referencia
fue ICV **en recién nacidos**. Ninguna de las dos condiciones es la del paciente.

### E3.2 — Experimento comparativo de vías
En ratones C448Y **adultos y sintomáticos**, comparar:
1. ICV
2. **Intratecal** (la vía humanamente plausible; precedente de éxito en HSP con MELPIDA /
   scAAV9-AP4M1 para SPG50, NCT06069687, Fase 1)
3. Intraparenquimatosa (solo como control de biodisponibilidad)

Cuantificar en cada vía:
- **Copia de genoma viral** por región (cervical, torácico, lumbar) por ddPCR.
- Transducción por tipo celular: **motoneuronas** (ChAT⁺), **axones CST** (SMI-32⁺), oligodendrocitos,
  astrocitos, y en DRG.
- Histología a 4 y 12 semanas post-inyección (persistencia transduccional).

### E3.3 — Restricciones de diseño del vector
- **Límite de empaquetado ~4,7 kb** entre los ITR. El cassette actual (dos cDNAs de SPAST +
  microRNA) está en el límite. Evaluar: vector de doble evento (split-intein), cDNA de M87
  únicamente + rescate de M1 por construcción, o promoters más compactos.
- **M1 se produce a partir de un codón de inicio débil/fugoso** (Piermarini 2025). Expresar
  M1 + M87 simultáneamente desde un cassette único requiere atención específica al balance de
  isoformas; reportar la razón M1:M87.
- Serotipos alternativos: AAV-PHP.B / AAV-PHP.eB son más eficientes en modelo murino pero tienen
  **problemas de-humanización** y una vía de toxicidad免疫 distinta documentada en primate. Si se
  cambian, hay que re-derivar toxicología. Nota: no sobre-usar el término aquí.

### E3.4 — Criterio de decisión
La vía que **gane** en transducción de CST con la menor toxicidad es la candidata a IND preclínico.
Este experimento consume presupuesto y debe financiarse **antes** de lamanufactura de GMP.

---

## 5. Eje 4 — El límite del silenciamiento: la proteína acumulada

### H4
> Silence-and-replace detiene la producción *nueva* de spastina mutante, pero no elimina la
> proteína M1 mutante ya acumulada, cuya semivida larga la convierte en el techo del beneficio.

### E4.1 — Cuantificar el techo
Medir, con anticuerpo específico de C448Y, la cantidad y la cinética de M1 mutante después del
tratamiento en animales adultos. Si el nivel no cae a niveles WT en 3–6 meses, el silenciamiento
está limitado por clearance, y eso es un **descubrimiento de mecanismo**, no un detalle.

### E4.2 — Intervenciones de clearance (candidatos a combinación)
Ordenados por encaje mecanístico, de mayor a menor madurez:

| Candidato | Mecanismo | Estado | Nota |
|---|---|---|---|
| **Inhibidor de HDAC6** (p.ej. tubastatina A) | M1 mutante hiperactiva HDAC6 → hipoacetilación de microtúbulos | Preclínico, *in vivo* en C448Y | **⚠ verificar la referencia de 2026** antes de construir el plan sobre ella |
| **AKV9 (antes NU-9)**, AKAVA Therapeutics | Inhibidor de agregación proteica | IND aclarada por FDA (jul 2023) para **ALS**; HSP figura como indicación; Ensayo Fase 1 en voluntarios sanos | Preclínico en C448Y; **⚠ verificar** los datos de 2026. No hay ensayo en SPG4 |
| Estabilizadores de microtúbulos (noscapina y análogos) | Compensan el déficit de seccionamiento | Preclínico | Least-validated de la lista |
| Moduladores de autofagia / proteasoma | Aclaramiento de M1 mutante | Explorar | Mecanismo no probado en SPG4 |

**Implicación estratégica:** la combinación de más valor y **menor riesgo regulatorio** es
`silenciamiento + fármaco small molecule`, no `silenciamiento + dispositivo`. Es además la
combinación que no depende de la vía de tSCS.

### E4.3 — Experimento
Diseño factorial en C448Y adultos tratados: {silence-and-replace} × {placeholder}, con y sin
candidato farmacológico. Endpoint sináptico idéntico al Eje 1, más carga de M1 mutante por Western
y microscopía confocal del CST.

---

## 6. Eje 5 — Translational: historia natural, biomarcadores, ensayos

### H5
> Los ensayos de SPG4 son inviables por tamaño porque la progresión es lenta (SPRS ~0,9 puntos/año);
> se necesita un biomarcador de progresión sensible y un *core outcome set*.

### E5.1 — El problema de potencia
La SPRS progresa ~0,9 puntos/año. Detectar una diferencia con un ensayo de 12 meses y n=30 es
marginal. **Sin un marcador速度快 intermedio, cualquier ensayo de intervención — incluido el de
tSCS — tendrá potencia insuficiente.**

### E5.2 — Biomarcadores candidatos, en orden de madurez
1. **MEP por TMS** (potenciales evocados motores): no invasivo, ya usado en ensayos de
   neuromodulación, correlaciona con función. Necesita estandarizar protocolo y umbral en HSP.
2. **Atrofia de tracto corticoespinal por RM** cuantitativa: más lento que la clínica, pero
   objective y no invasivo.
3. **Neurofilamento en suero** como marcador de degeneration axonal.
4. **Biomarcadores digitales**: sensores de marcha (acelerometría, longitud de paso en дома /
   exterior, doble tarea). Alta frecuencia de medida, pero sin validación en HSP.

### E5.3 — Recursos existentes para收养
Registros de HSP con cohortes natural-history: **CoR-HSP (NCT06553976)**, HSP de inicio precoz
(NCT04712812), PROSPAX, ARCA, STOP-HSP.net. **Afiliarse a un registro en lugar de crear el propio**
reduce años de tiempo de seguimiento. Revisar si hay cohorte SPG4 genotipada.

### E5.4 — *Core outcome set*
La revisión de Cipriano et al. (*Ther Adv Neurol Disord* 2026, PMID 41537160) documenta ~40 ensayos
registrados en HSP a junio de 2025, casi todos sintomáticos u observacionales, y la **ausencia de un
core outcome set** como el principal obstáculo para el diseño de ensayos de SPG4. Desarrollar o
adoptar un COS de SPG4 es una contribución de campo, no un trámite, y es屈 barato comparado con un
ensayo fallido.

---

## 7. Eje 6 — tSCS: diseño, competencia y regulación

### H6
> Un ensayo de tSCS en SPG4 sin control sham no es interpretable, y la indicación regulatoria
> existente excluye enfermedad progresiva.

### E6.1 — La=colisión regulatoria
El ARC-EX de ONWARD Medical obtuvo De Novo (**DEN240014**, dic-2024) con indicación de
**"non-progressive neurological deficit"** (código de producto SDO, 21 CFR 890.5851). SPG4 es
progresivo por definición. Consecuencias:
- La vía **510(k) está cerrada** para SPG4.
- Un **De Novo nuevo** con pivotal propio es caro y lento.
- El propio De Novo de ARC-EX significativamentesubmitió que no hay beneficio funcional demostrado en
  recuperación neurológica.

**Conclusión operativa:** tSCS **no tiene una ruta regulatoria rápida** en SPG4. Presentarlo como
"listo para clínica" sería un error de$$$，（此处原文即被截断）── hay que corregir esa expectativa en el
plan.

### E6.2 — La#precedente negativo
El único ensayo con sham de tSCS en una enfermedad **progresiva** (Spieker et al., *Front Neurol*
2025, PMID 40917667, EM progresiva, n=20, crossover) fue **negativo** en el endpoint primario
(efecto pequeño, no significativo) y **la marcha favoreció al sham**. El problema de ceguera en
tSCS es real y documentado: la fase de *ramp-up* es en sí misma un descriptor de grupo.

**Implicación:** un diseño de un solo brazo abierto, n=15, sin sham, **no puede generar una
conclusión defendible.** Este es el punto de diseño más importante del programa de tSCS.

### E6.3 — La competencia
**NCT07417943** (Univ. Kentucky / Sachdeva + Spastic Paraplegia Foundation, *recruiting*, n=15):
tSCS 2×/semana × 8 semanas (16 sesiones), 1 h, electrodos lumbares, SCONE. Primarios: 10MWT, 6MWT,
MAS, SPRS. Secundarios: HSP-SNAP, marcha 3D, sit-to-stand, fuerza de rodilla (Biodex).
Excluye bombas intratecales y cambios recientes en baclofeno/botox.

Es un diseño casi idéntico al que se describe. **Decisión requerida:**
- **(a) Coordinarse**: recruited del mismo pool, armonizar endpoints,arkers, compartir biomarcadores.
  Maximiza el poder estadístico de la literatura de tSCS en SPG4 (que hoy es: 1 caso aislado +
  una serie de 18 sin control, epidural).
- **(b) Diferenciarse**: un ensayo con sham, y/o centrado en un biomarcador mecanístico en lugar
  de en la escala clínica.

La opción (a) es la más eficiente y la menos-costosa. La (b) solo tiene sentido si hay una hipótesis
mecánica adicional que el equipo del otro ensayo no captura.

### E6.4 — Dónde tSCS sí gana
- **Como assay de plasticidad**: la tSCS modula la excitabilidad de circuitos medulares. La evidencia
  de que mejora la inhibición espinal postsináptica (Cell Rep Med 2024, PMID 39532101) sugiere que
  tSCS puede **medir** si un circuito medular sigue siendo plástico. Eso la convierte en un
  **marcador de ventana terapéutica** más que en una terapia.
- **Como plataforma de sinergia**: la combinación tSCS + entrenamiento específico de tarea
  funcionó en lesión medular y en ictus. El principio — la estimulación enable la plasticidad, el
  entrenamiento la consolida — es transferible a HSP si el entrenamiento es lo que se usa para
  mantener la función.
- **El riesgo de empeoramiento**: la estimulación dual ha **aumentado** la espasticidad del sóleo
  en el subgrupo con espasticidad basal baja. Perfilar la espasticidad basal como biomarcador de
  respuesta antes de escalar.

---

## 8. Eje 7 — Estrategia de cartera

**No construir un combination product.** La complejidad regulatoria de génico + dispositivo
(producto combinatorio, vía CBER) es desproporcionada frente al valor en esta fase. Son dos
programas paralelos con lógicas distintas:

| | Programa génico | Programa tSCS |
|---|---|---|
| Objetivo | Modificar la enfermedad | Medir plasticidad + habilitar función |
| Horizonte | 5–8 años | Ahora |
| Riesgo principal | Eje 3: delivery en adulto | Eje 6: sin sham no es interpretable |
| Valor único | El activo que se puede vender/licenciar | El generador de datos humanos y marcadores |
| Interacción | La tSCS puede **proporcionar el readout** para el ensayo génico | — |

**La conexión de valor:** el programa de tSCS genera, en 12–18 meses, un dataset humano de SPRS,
marcha, MEPs y seguridad de estimulación en SPG4. Ese dataset es exactamente lo que el programa
génico necesita para **dimensionar su ensayo futuro** y para **calibrar su biomarcador** (Eje 5). El
programa tSCS, aunque no sea el asset, **paga parte del camino** del génico.

---

## 9. Escalera de des-riesgo (orden recomendado)

```
FASE 1 — Meses 0-6   E1.2  Brazos con control silence-only en adultos sintomáticos
                     E2.1  Mapeo de ventana + E2.2 dosimetría
                     E3.2  Comparación ICV vs intratecal en adulto  ← en paralelo
                     E5.1  Ingreso a CoR-HSP (NCT06553976) para historia natural propia
                            ↓
                    DECISIÓN 1: ¿Hay beneficio en sintomáticos?  Si NO → replantear
                            ↓
FASE 2 — Meses 6-18  E4.1  Cinética de la M1 mutante residual
                     E4.2  Factorial: silence-and-replace × fármaco de clearance
                     E3.3  Optimización del cassette (packing, ratio M1:M87, serotipo)
                     E5.2  Validación de biomarcadores (TMS-MEP, RM, digital)
                            ↓
                    DECISIÓN 2: ¿Hay beneficio incremental del fármaco?  → define la combinación
                            ↓
FASE 3 — Meses 18-30 E1.4  Confirmación de eficacia en lote de GMP
                     Toxicology, duración de expresión, reversibilidad
                     E6.3  Decisión: coordinar o diferenciar el ensayo de tSCS
                            ↓
FASE 4 — Meses 30+   Eje 5: mecanismo de lectura para el primer ensayo en humanos
                     Eje 6:5: vía regulatoria de tSCS (De Novo) — SOLO si el programa sobrevive
```

**Presupuesto por fase:** Fase 1 es la de mayor retorno por dólar y debe financiarse completa antes
de comprometer cualquier gasto de GMP. Fase 3 es la más cara y es la que no se debe iniciar hasta
que Fase 1 haya Take el programa.

---

## 10. Riesgos principales

| Riesgo | Probabilidad | Impacto | Mitigación |
|---|---|---|---|
| El silenciamiento no funciona en animales ya sintomáticos | Media | **Fatal** — sin mercado | Eje 1 **primero**, no después |
| AAV9 no alcanza CST en adulto | Alta | Fatal | Eje 3 en paralelo desde el mes 0 |
| El cassette no empaqueta bien / ratio M1:M87 incorrecto | Media | Alto | E3.3 temprano, cuantificar siempre |
| Toxicidad de sobreexpresión de spastina | Media | Alto | E2.2: techo explícito, expresión fisiológica |
| tSCS sin sham → resultado no interpretable | **Cercana a 1** si no se cambia | Alto | E6.2: sham obligatorio |
| Conflicto con NCT07417943 | Media | Medio | E6.3: Decide antes de abrir centros |
| Ausencia de core outcome set | Alta | Medio | E5.4: contribución de campo |

---

## 11. Lo que hay que verificar antes de comprometerse

Estas referencias provienen de una búsqueda automatizada y **no han sido confirmadas una por una
contra el original**. Verificar cada una antes de construir presupuesto sobre ella:

- **⚠ Referencia crítica:** el trabajo de HDAC6 / tubastatina A en SPG4 (*Cell Rep* 2026). Es el
  candidato farmacológico másComputer del Eje 4, pero **si esta referencia no se confirma, todo el
  Eje 4 necesita rehacerse.**
- **⚠ Referencia crítica:** los datos preclínicos de AKV9/NU-9 en ratones C448Y.
- La referencia de Sproesser-Koch et al. 2021 sobre caso único de SPG4 con SCS epidural.
- El estado de registro actualizado de NCT07417943 (el registro puede haber cambiado desde la
  fecha de esta búsqueda).
- Confirmar que no existe un ensayo de tSCS en HSP que no esté en la base de datos de la búsqueda.

Verificado y sólido (no requiere verificación): Piermarini 2025 (PMID 41311060), Up-LIFT (PMID
38769431), ARC-EX De Novo DEN240014, MELPIDA / NCT06069687, C448Y (PMID 30520996), OMIM 604277,
Spieker 2025 (PMID 40917667, verificable directamente).

---

## 12. Qué pedir en la primera reunión del equipo

1. ¿Cuál es el vector exacto? Carga útil, serotipo, tamaño total, y **el ratio M1:M87 reportado**.
2. ¿Cuál es la línea base de la colonia: progresión de marcha por edad, y varianza? (¿Permite
   distinguir un efecto real del ruido, o se necesita un colony más grande?)
3. ¿Hay datos de Western blot con anticuerpo específico de C448Y después del tratamiento? (Esto es
   el Eje 4, y puede que ya lo tengan sin haberlo interpretado como tal.)
4. ¿Se cuantificó la carga de genoma viral en médula espinal, o solo hubo evaluación funcional?
5. ¿Quiénes son los pacientes reales que se busca incluir? La edad de la población diana y el
   estado de la degeneración CST determinan la claims del ensayo génico.
6. ¿El tSCS va a ser el mismo dispositivo y los mismos parámetros que el de Kentucky? Si sí,
   E6.3 es urgente.

---

*Documento de trabajo. Las cifras y referencias marcadas ⚠ requieren verificación primaria.*
