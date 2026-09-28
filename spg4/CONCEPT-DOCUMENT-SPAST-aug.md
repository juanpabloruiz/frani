# Concept Document: SPAST Gene Augmentation Therapy for SPG4

## A Simplified Therapeutic Approach for Haploinsufficient SPAST Variants

**Project codename:** SPAST-aug
**Document version:** 1.0 — September 2026
**Origin:** Patient-initiated. Proposed by a patient with a documented pathogenic SPAST splice variant and independent preclinical work.

---

## RESUMEN EN ESPAÑOL (para el paciente / para revisión rápida)

**Qué es este documento.** Una propuesta de terapia génica para SPG4, la paraparesia espástica
hereditaria más común. No es una cura. Es un plan para frenar la progresión.

**La variante del paciente.** `SPAST NM_199436:c.1226-2A>G`, heterocigota, patogénica. Es una
mutación de sitio de empalme (splice) en el aceptor 3' canónico del exón 10.

**Por qué esto simplifica el problema.** Las mutaciones de SPAST se dividen en dos clases con
mecanismos distintos:

- **Missense** (p. ej. C448Y, el modelo de ratón más usado): la proteína mutante se acumula y
  **envenena** las neuronas. Requiere silenciar **y** reponer — dos mecanismos en un solo vector.
- **Splice / nonsense / frameshift / deleciones** (esta variante): solo **falta cantidad** de
  proteína. La copia mutante no aporta proteína tóxica. No hay nada que silenciar.

Esta última clase es formalmente la **más simple de tratar**, porque "traer una copia que funcione"
es la modalidad de terapia génica más madura del mundo, con múltiplesapprobaciones regulatorias.

**El argumento central del documento.** Los datos preclínicos previos del paciente se realizaron
con un constructo de *silence-and-replace* (microRNA anti-SPAST + dos cDNAs), modelado sobre
C448Y. **Ese constructo resuelve un problema más difícil del que esta variante presenta.** Para
una pérdida de función se requiere únicamente **un único cDNA de SPAST sano bajo un promotor
neuronal específico**, sin microRNA, sin cassette doble, y con tamaño de carga que **cabe en un
vector AAV autocomplementario (scAAV)**, lo que reduce la dosis y acelera el inicio de expresión.

**Lo más importante del documento.** Esta terapia no sería específica de un paciente. **La
mayoría de las mutaciones de SPAST patógenas son de pérdida de función** (splice, nonsense,
frameshift, deleciones de exones). Una terapia de aumento génico cubriría a ese grupo amplio, no
solo a los pacientes con mutación missense — que son la minoría y donde el *silence-and-replace*
es el único enfoque posible. Esto amplía sustancialmente el alcance científico y comercial.

**Lo que pido.** No pido permiso para hacer esto. Pido dos cosas concretas: (1) que un laboratorio
experto confirme la consecuencia molecular exacta de esta variante, y (2) que se evalúe si el
programa merece asignación de recursos. La sección 6 lista los experimentos bloqueantes, y el
primero es barato y de meses, no de años.

---

## 1. Executive Summary

**The ask.** Evaluate and, if endorsed, resource a **gene augmentation therapy for SPG4** — the
addition of a single wild-type human *SPAST* cDNA, delivered by intrathecal AAV, without any
silencing component.

**Why now, and why this is not a moonshot.**

1. **There is no approved disease-modifying therapy for SPG4.** Management is limited to symptom
   control and physical therapy (Cipriano et al., *Ther Adv Neurol Disord* 2026, PMID 41537160;
   review of ~40 registered HSP trials, almost all symptomatic or observational).

2. **The therapeutic logic is unusually simple for a dominant neurodegenerative disease.** The
   variant class that predominates in SPAST — splice, nonsense, frameshift, exon deletions — acts
   by haploinsufficiency. The therapeutic requirement is therefore *augmentation*, not *correction*.
   Augmentation is the most de-risked modality in gene therapy, with approved products and an
   established regulatory template.

3. **The human delivery route already exists.** Intrathecal AAV9 has an active Phase 1 / Phase 3
   precedent in a sibling HSP subtype: MELPIDA (scAAV9-AP4M1) for SPG50, NCT06069687 and
   NCT06692712, dosing 1×10¹⁵ vg, ages 4–72 months. The question "can intrathecal AAV9 reach and
   treat the relevant spinal motor neurons in humans" has been answered *for a closely related
   disease*, and the answer is yes.

4. **A vector already built in this program is over-engineered for this variant.** The prior
   silence-and-replace construct (AAV9 + anti-SPAST microRNA + M1 and M87 cDNAs, delivered ICV in
   neonatal SPAST-C448Y mice; Piermarini et al., *Mol Ther* 2025, PMID 41311060) addresses both a
   toxic gain-of-function and a loss-of-function. The variant in question has **only** the
   loss-of-function component. Roughly half the payload can be deleted, and the remaining payload
   fits within the self-complementary AAV capacity envelope.

5. **The addressable population is large relative to other ultra-rare neuro targets.** *SPAST* is
   the most frequently mutated gene in autosomal dominant HSP (~40% of genetically resolved AD-HSP;
   GeneReviews NBK1160). Prevalence 2–6 per 100,000.

**The honest limitation, stated up front.** Augmentation is expected to **halt or slow
corticospinal degeneration, not reverse it.** Established axonal loss in a 46-year-old patient
with a 7-year symptom history is not recoverable with current technology. The development goal must
therefore be framed as **disease modification by stabilisation**, and the first trial must be
designed and powered for that claim. A project promising regeneration should be treated with
scepticism by any reviewer, including this one.

---

## 2. The Patient and the Variant

| Field | Value |
|---|---|
| Patient | Single index patient (initiator of this document), male, 46 years |
| Country of residence | Argentina |
| Presumptive diagnosis | Hereditary spastic paraplegia, pre-diagnostic referral |
| Genetic report | GEND@omics, protocol 36925, dated 27 December 2018 |
| Method | Clinical exome (Illumina HiSeq 4000, SureSelect Clinical Research Exome V2) |
| Reference genome | NCBI build 37 / hg19; BWA + GATK haplotypecaller; ANNOVAR annotation (RefSeq, dbSNP147, ClinVar, gnomAD) |
| **Finding** | **`SPAST` `NM_199436:exon10:c.1226-2A>G`, heterozygous, classified pathogenic** |
| Rationale in report | Absent from gnomAD; deleterious by computational prediction; located at a canonical splice site, predicting a loss-of-function effect — a recurrent mutational mechanism in SPAST-HSP |
| Current status | Walks with an assistive device |

**Interpretive confidence and its limits.** The report explicitly states that variants were not
confirmed by an independent method and may represent technical artefacts. The c.1226-2A>G
call should therefore be **orthogonally confirmed** (Section 6.1). Note also that the assay was
performed in 2018; the laboratory's own limitations section notes that some variant types are
systematically undetectable by exome sequencing. A current re-analysis on a modern panel or
whole-genome sequence is recommended as standard of care, independent of this programme.

**Transcript note.** The report cites `NM_199436`. Exon numbering differs across SPAST transcripts
in the literature; the report's exon-10 designation should be re-anchored to the current RefSeq
annotation before designing any construct, and the splice junction confirmed in patient RNA
(Section 6.1).

---

## 3. Mechanism: Why This Variant Is the Simple Case

### 3.1 The two mechanistic classes in SPAST

The literature separates SPAST mutations by mechanism, and this distinction is the single most
important fact in this proposal.

> "The findings suggested that the haploinsufficiency is the pathogenic mechanism for SPG4, whereas a
> dominant-negative effect is the pathogenic mechanism for SPG3A."
> — OMIM 604277 (*SPASTIN; SPAST*)

> "The (2006) authors predicted that the **splice site and truncation mutations decreased overall
> spastin function, implying haploinsufficiency as a pathogenic mechanism, whereas the missense
> mutations likely resulted in a dominant-negative pathogenic mechanism and a more severe
> phenotype.**"
> — OMIM 604277

> "Many mutations reduce the abundance of full-length, sequence-normal spastin, such as mutations
> that cause premature translation termination, insertions, or deletions that lead to nonsense
> transcripts and mutations that cause aberrant messenger RNA splicing."
> — Fink JK, *Arch Neurol* 2003 (cited in JAMA Neurology, "The Hereditary Spastic Paraplegias: Nine
> Genes and Counting")

The variant reported in this patient is a canonical splice-site substitution, placing it in the
first category by prediction.

### 3.2 What haploinsufficiency implies therapeutically

If the mutant allele fails to produce functional spastin and the remaining wild-type allele
supplies insufficient quantity, then:

- **Silencing the mutant allele is therapeutically inert.** There is no toxic transcript or
  protein to remove. This is the central reason the existing construct carries machinery this
  variant does not need.
- **Delivering additional wild-type SPAST is mechanistically sufficient in principle.** The
  therapeutic requirement reduces to raising intracellular spastin toward the normal homozygous
  range.
- **The dose target is knowable.** Because the deficit is quantitative, there is an empirically
  measurable therapeutic window between "insufficient" and "toxic," and that window is directly
  measurable in patient-derived cells (Section 6.2). Dominant-negative disease offers no such
  clean target, because there is no safe dose of the wild-type protein that counteracts a
  continuously interfering mutant.

### 3.3 Why half the spastin is not enough

*SPAST* encodes spastin, an AAA+ ATPase that severs microtubules. Microtubule arrays are
continuously remodeled; severing regulates microtubule number, length, and mobility, and also
endosomal tubulation and ER morphogenesis. *SPAST* is expressed from two initiation codons,
producing M1 (the longer isoform, bearing a hydrophobic N-terminal domain) and M87 (the shorter,
more abundant isoform in neuronal and non-neuronal tissue).

Adequate severing activity is required for **axonal transport**, which is the function with the
greatest length dependence. Axons of the corticospinal tract are the longest in the human body, so
they are the first to fail under a partial spastin deficit. This length dependence produces the
characteristic clinical pattern: distal lower-limb involvement, the longest fibres degenerating
first, and the "dying-back" progression that distinguishes SPG4-HSP from other upper motor neuron
disorders.

This also explains the clinical observation that motivates this project: **the target pathology is
quantitatively defined, anatomically specific, and located in a compartment (intrathecal CSF) that
is already routinely accessible.**

### 3.4 A nuance that must not be skipped

Not every LoF-class variant behaves identically. If the c.1226-2A>G allele produces a **stable,
partially functional truncated protein**, augmentation alone may be insufficient, and the residual
product must be characterised. Conversely, if the transcript is destroyed by nonsense-mediated
decay, the allele is a clean null and augmentation is mechanistically complete. **The design of
this therapy therefore cannot be finalised until the molecular consequence is measured in patient
cells.** That measurement is Experiment 6.1 and is deliberately placed first.

---

## 4. From the Existing Construct to a Simplified One

### 4.1 What has already been built

A silence-and-replace AAV9 vector was constructed and tested:

- **Payload:** anti-*SPAST* microRNA (silences endogenous *SPAST*) + cDNA of human M1 + cDNA of
  human M87
- **Route:** intracerebroventricular
- **Model:** *SPAST*-C448Y transgenic mice
- **Dosing stage:** neonatal, at birth
- **Result:** endogenous spastin replaced by healthy spastin at physiological levels; onset and
  progression of corticospinal degeneration and gait defects prevented

This is a sound and well-executed experiment. It is a direct replication of the design reported by
Piermarini, Guha, Qiang, Gray-Edwards, Sena-Esteves and Baas (*Mol Ther* 2025, PMID 41311060).

### 4.2 Two limitations of that experimental design, and their consequences

**Limitation 1 — the model does not match this patient's mechanism.** *C448Y* is a missense
variant in the AAA domain. The published characterisation is explicit that the C448Y mutant
"did not behave in a dominant-negative fashion" and that its lysosomal transport deficits "were
exacerbated by knockdown of endogenous spastin and could be rescued by [re-expression]" (OMIM
604277). That is a model whose phenotype depends on silencing the endogenous allele — the exact
opposite of a clean null allele. **A therapeutic rationale validated on C448Y does not transfer to
a splice-site haploinsufficient variant without argument.**

**Limitation 2 — prophylactic dosing does not address the target population.** SPG4 typically
presents in adulthood. A treatment administered at birth in mice cannot be delivered at birth in
humans; the relevant experiment is treatment of **symptomatic adult** animals. This is
independently acknowledged as the unresolved next step by the authors of the reference work.

### 4.3 The simplified construct

| Feature | Existing (silence-and-replace) | Proposed (SPAST-aug) |
|---|---|---|
| **Silencing element** | Anti-*SPAST* microRNA | **None — not required for a null allele** |
| **Transgene** | cDNA M1 + cDNA M87 (dual) | **Single wild-type *SPAST* cDNA** |
| **Promoter** | Not specified in available abstract | **Neuron-restricted** (e.g. SYN1 / CAMK2A / MEF2C class) |
| **Vector format** | ssAAV9 | **scAAV9** (payload fits) |
| **Anticipated mechanism** | Remove toxin + restore quantity | **Restore quantity only** |
| **Toxic protein clearance needed?** | Yes — accumulated mutant M1 is long-lived and not cleared by mRNA silencing | **No** |

**Design rationale, point by point.**

1. **Deletion of the silencing cassette is mechanistically justified, not a simplification for its
   own sake.** For a null allele there is no toxic product. Silencing endogenous *SPAST* in a
   patient already at ~50% spastin would, if the patient's allele is not a complete null, actively
   *worsen* the deficit.

2. **A single *SPAST* cDNA reduces payload from a near-packaging-limit construct to a
   comfortable one.** *SPAST* M87 is ~528 aa; M1 is ~616 aa. A single cDNA plus a compact neuronal
   promoter and polyadenylation signal fits well within the ~4.7 kb AAV capacity, and **within the
   ~2.4 kb self-complementary AAV capacity** — subject to confirmation of exact isoform length.
   This is a material advantage: scAAV halves the time to therapeutic expression, which matters in
   a progressive disease, and reduces total vector genomes required.

3. **Consideration — a single M1-encoding cDNA may yield both isoforms.** Because M87 is produced
   by internal translation initiation from a weak/leaky downstream start codon, an M1-length cDNA
   may generate M1 and M87 by re-initiation, reproducing the physiological isoform ratio with one
   transgene. **This is a hypothesis requiring experimental verification** (Section 6.3), not an
   assumption. If it holds, it is the more elegant design; if not, a single M87 cDNA is the
   fallback and is the smaller payload.

4. **A neuron-restricted promoter widens the therapeutic window.** Spastin overexpression is
   cytotoxic, and spastin is also required for mitotic spindle disassembly in dividing cells.
   Restricting expression to neurons — ideally to the relevant motor neuron population —
   decouples the therapeutic dose from the mitotic toxicity ceiling. This is the single most
   important design decision in the construct and should be treated as such.

5. **Route: intrathecal.** The reference preclinical work used ICV delivery **in neonatal mice**.
   For an adult human this is not transferable: systemic AAV9 transduction of the CNS is
   age-dependent and markedly reduced in adults, and the therapeutic target is the longest axons in
   the body. Intrathecal delivery targets the CSF compartment directly, bypassing the
   blood–brain barrier, and has an active human precedent in HSP (MELPIDA, scAAV9 intrathecal,
   SPG50).

---

## 5. What This Programme Would Actually Deliver

**Claim to pursue:** addition of wild-type *SPAST* by intrathecal scAAV9 **reduces or arrests
corticospinal degeneration in SPG4 patients harbouring loss-of-function *SPAST* variants.**

**Claim to avoid:** reversal of established neurological deficit. No current modality regenerates
degenerating corticospinal axons, and a proposal asserting otherwise should be rejected on those
grounds alone.

**Applies to more than this patient.** Loss-of-function variants — splice, nonsense, frameshift,
and exon deletions — constitute a substantial share of pathogenic *SPAST* variants. A large
fraction of diagnosed SPG4 population would therefore be eligible. This is a
**breadth-of-application argument that distinguishes this programme from an allele-specific
approach**, and it is the main reason to build augmentation rather than silence-and-replace.

---

## 6. Staged Development Plan

Staged so that **each stage can terminate the programme cheaply before the next is funded.** The
first stage is months, not years, and requires no gene therapy manufacturing.

### 6.1 STAGE 1 — Molecular characterisation of the variant (BLOCKING, low cost, high value)

**Nothing else should be funded until this is complete.** The therapeutic design in Section 4.3 is
conditional on the outcome.

| # | Experiment | Method | Resolves |
|---|---|---|---|
| 1.1 | Confirm the variant | Sanger or amplicon NGS on patient blood; confirm zygosity | Report caveat: unconfirmed by independent method |
| 1.2 | Determine inheritance | Test parents; if inherited, cascade to siblings | De novo vs. inherited; family risk |
| 1.3 | Map the splice consequence | RT-PCR + Sanger across the exon 9–10–11 region on patient fibroblast RNA | Exon 10 skipping? cryptic acceptor? |
| 1.4 | Test for NMD | Parallel RT-PCR ± cycloheximide | Is the transcript destroyed? (clean null) |
| 1.5 | Detect cryptic transcripts | Long-read RNA sequencing | Unannotated acceptors, exon 9/10 boundary |
| 1.6 | Determine protein product | Western blot, anti-spastin; plus antibody raised against the predicted truncated product | Is a stable truncated protein made? |
| 1.7 | Re-anchor exon numbering | Current RefSeqSeq annotation | Transcript/exon coordinate consistency |
| 1.8 | Re-analyse the genome | Current-panel or WGS re-analysis | Diagnoses missed by 2018 exome |

**Decision gate 1.** If the allele is a **clean null** (NMD-transduced, no stable protein):
augmentation is mechanistically complete → proceed to Stage 2. If a **stable, partially functional
truncated protein** is produced: augmentation may be insufficient → the variant requires
characterisation against the patient's own cells before proceeding, and the programme reverts to a
two-component design.

**Deliverable:** a confirmed molecular diagnosis. Independently valuable to the patient regardless
of the therapy programme.

### 6.2 STAGE 2 — Quantify the deficit and the therapeutic window

| # | Experiment | Method | Resolves |
|---|---|---|---|
| 2.1 | Quantify the deficit | Quantitative Western / targeted mass spectrometry, patient cells vs. two controls | Absolute spastin levels; the actual degree of insufficiency |
| 2.2 | Define the rescue dose | Titrate *SPAST* expression in patient-derived cells; measure microtubule dynamics, axonal transport, lysosomal trafficking | How much is needed for full rescue |
| 2.3 | Define the toxicity ceiling | Escalate expression; assess microtubule stability, proliferation, viability | The upper dose limit |
| 2.4 | Select the promoter | Compare candidate neuronal promoters for level, specificity, and duration | Therapeutic index |
| 2.5 | **Therapeutic window** | From 2.2 and 2.3 | **The margin between rescue and toxicity — the programme's central safety number** |

**Decision gate 2.** A therapeutic window of less than ~3-fold between the effective dose and the
toxic dose is a poor candidate for gene therapy, where dose cannot be titrated after administration
and cannot be withdrawn. **This gate can terminate the programme at low cost and should be run
early.**

### 6.3 STAGE 3 — Model and construct

| # | Experiment | Notes |
|---|---|---|
| 3.1 | Verify the M1-reinitiation hypothesis | Does an M1 cDNA alone yield the physiological M1:M87 ratio? |
| 3.2 | Build the construct | scAAV9, neuron-restricted promoter, single *SPAST* cDNA; confirm titre, purity, residual plasmid, sterility |
| 3.3 | **Generate a faithful model** | See below |

**On the model — this is a substantive gap in the current data.** A knock-in mouse carrying the
human `c.1226-2A>G` in *Spast*, made heterozygous against a null allele, would reproduce the
patient's actual genotype and therefore the actual therapeutic logic. The existing C448Y model does
not. Failing that, **corticospinal-neuron-like cells differentiated from the patient's own iPSCs**
are a physiologically relevant, patient-specific system, and the Qiang laboratory has established
relevant corticospinal differentiation protocols. The Awad/Gautam proteomic work on FACS-purified
corticospinal neurons in the C448Y model (Neurobiol Dis 2026) is a methodological precedent for the
approach.

**Decision gate 3.** A model in which augmentation produces a measurable, quantitative rescue.

### 6.4 STAGE 4 — Efficacy in symptomatic animals (THE PIVOTAL EXPERIMENT)

**This is the experiment that determines whether the programme has clinical value, and it has not
been done.**

- **Design:** treat animals at **symptomatic** age, not at birth. The existing data are
  prophylactic and therefore address a population that does not exist clinically.
- **Two arms are mandatory:**
  - **Augmentation alone** — the proposed therapy.
  - **Augmentation + silencing** — the existing construct, as an active comparator.
- **Controls:** wild-type + empty vector; heterozygous mutant + empty vector; dose groups.
- **Endpoints:** corticospinal axon density and neurofilament immunostaining; spinal cord atrophy;
  gait metrics (CatWalk); **and a stability endpoint — the divergence between treated and
  age-matched untreated decline over time**, which is the endpoint that matches the stabilisation
  claim.
- **n:** powered from the colony's own variance, not assumed. Sex as a factor, not pooled.
- **Pre-registration** of design and analysis before groups are unblinded.

**Decision gate 4.** Benefit in symptomatic animals → proceed to IND-enabling work. No benefit →
the programme is prophylactic-only, which is not applicable to adult-onset SPG4, and **should be
terminated.**

### 6.5 STAGE 5 — Delivery, dose and safety

- **Route comparison in adult animals:** intrathecal (primary) vs. ICV (comparator).
- **Dose-ranging with safety endpoints:** dorsal root ganglia toxicity is a documented AAV
  dose-limiting effect; hepatic and immune monitoring; peripheral transduction.
- **Confirm target engagement:** vector genome copies and *SPAST* protein in motoneurons and
  corticospinal axons, at physiological — not supraphysiological — levels.
- **Duration of expression:** chronic, and whether any drift or silencing occurs.

### 6.6 STAGE 6 — Biomarkers and trial design

**The programme's trial-readiness problem is statistical, not biological.** The Spastic Paraplegia
Rating Scale progresses approximately 0.9 points/year in prospective cohorts. A 12-month trial in
30 patients cannot reliably detect a modest slowing. **Without a sensitive intermediate biomarker,
this therapy will fail in phase 2 for statistical reasons despite working.**

| Biomarker | Role | Status |
|---|---|---|
| TMS motor-evoked potentials | Non-invasive, rapid, functional; correlates with gait | Highest priority |
| Quantitative MRI corticospinal atrophy | Objective, non-invasive, slow | Complementary |
| Neurofilament light (serum) | Axonal degeneration | Exploratory |
| Digital gait biomarkers | High-frequency measurement | Exploratory, unvalidated in HSP |

**Parallel action, required now and independent of the therapy:** enrolment in a natural-history
registry — **CoR-HSP (NCT06553976)**, or the early-onset cohort (NCT04712812) if eligible.
Cipriano et al. (2026) document the **absence of a core outcome set in HSP** as the principal
obstacle to SPG4 trial design. Contributing to, or adopting, a core outcome set is cheap and is a
genuine contribution to the field.

---

## 7. Risks and Mitigations

| # | Risk | Severity | Mitigation | Mitigation stage |
|---|---|---|---|---|
| R1 | Augmentation does not arrest degeneration in symptomatic animals | **Programme-ending** | Stage 4 is the pivotal experiment; run early, run it properly | 4 |
| R2 | Therapeutic window too narrow (rescue dose ≈ toxicity dose) | **Programme-ending** | Neuron-restricted promoter; Stage 2 gate | 2 |
| R3 | The variant is not a clean null; truncated protein retains function | Design-changing | Stage 1 RNA and protein work | 1 |
| R4 | AAV9 does not reach corticospinal axons in adult human at tolerable dose | High | Intrathecal route; human precedent in SPG50 | 5 |
| R5 | Dorsal root ganglia toxicity at required dose | High | Dose-ranging; monitor | 5 |
| R6 | Therapeutic claim over-reaches (promising reversal) | High — credibility risk | Claim stabilisation only; state it in the protocol | All |
| R7 | Competitive: an SPAST-directed programme reaches the clinic first | Medium | This is a *complementary* variant class, not head-to-head; first-in-class for augmentation | — |
| R8 | Programme runs on an unverified variant call | High | Stage 1.1 confirmation | 1 |
| R9 | Patient-specific framing narrows scientific value | Medium | Reframe as a variant-class strategy applicable to LoF SPG4 broadly | All |
| R10 | No validated biomarker → phase 2 underpowered despite efficacy | High | Registry enrolment; biomarker validation in parallel | 6 |

---

## 8. Regulatory and Clinical Precedent

| Element | Status |
|---|---|
| **Closest human precedent** | **MELPIDA** (scAAV9-AP4M1, intrathecal) for **SPG50**. Phase 1 **NCT06069687**; Phase 3 **NCT06692712**. Dose 1×10¹⁵ vg; ages 4–72 months. Orphan drug and rare pediatric disease designations granted |
| **Lesson from precedent** | An HSP gene therapy reaching phase 1 and phase 3 on an intrathecal AAV9 route is a demonstrated, non-hypothetical path. Route, dose range, immunogenicity monitoring and safety surveillance can be inherited from this programme |
| **US orphan designation** | Should be available — *SPAST*-HSP prevalence 2–6/100,000. **Not currently confirmed for SPG4 specifically** and should be verified |
| **Inheritance pathway** | Augmented gene therapy follows the established precedent rather than a novel modality; no in vivo editing |
| **Primary regulator** | FDA CBER. If a device is added to the programme, the combination-product pathway is materially more complex — see Section 9 |
| **Trial geography** | Specialist HSP trial capacity is concentrated in North America and Europe. An Argentine participant would likely require travel for intervention trials; natural-history registries are the more realistic immediate entry point |

---

## 9. Scope Boundary: What This Programme Does *Not* Include

**Transcutaneous spinal cord stimulation (tSCS) is explicitly outside this proposal.** Rationale,
stated so it is not reopened without new data:

1. **Regulatory.** The available tSCS devices are authorised (FDA De Novo, product code SDO, 21 CFR
   890.5851) for **non-progressive** neurological deficit following spinal cord injury. SPG4 is
   progressive by definition. A 510(k) pathway is therefore unavailable; a new De Novo
   classification with its own pivotal trial would be required. The indication language is a
   structural obstacle, not a formality.

2. **Evidence transfer.** The strongest tSCS efficacy data are from spinal cord injury and stroke,
   where the substrate is a stable lesion with spared tissue. The closest degenerative analogue —
   a sham-controlled randomised crossover trial in **progressive multiple sclerosis** — was
   **negative** on its primary endpoint, with gait measures favouring sham (Spieker et al.,
   *Front Neurol* 2025, PMID 40917667). Extrapolation from SCI to SPG4 is therefore not merely
   speculative; it has been directly tested in an adjacent indication and has failed.

3. **Blinding.** A parallel sham-controlled tSCS trial in hereditary spastic paraplegia is
   registered (NCT07417943, University of Kentucky with the Spastic Paraplegia Foundation,
   recruiting, n=15, open-label, single-arm). **An open single-arm design cannot separate a
   treatment effect from a placebo effect**, and the sham-controlled evidence in a progressive
   disease suggests the placebo component is substantial.

4. **Combination product.** Combining a gene therapy with a device creates a combination-product
   regulatory pathway that is disproportionate to the current stage of the gene therapy
   programme. **Recommendation: do not pursue a combination product.** Run the two efforts as
   independent programmes. tSCS data may still prove useful as a **functional biomarker of spinal
   circuit plasticity** — a secondary question, not a therapeutic claim.

---

## 10. Programme Summary and Asks

### Timeline (indicative, from Stage 1)

```
Stage 1  Molecular characterisation      months 0–6      LOW COST — terminates the programme early if it fails
Stage 2  Deficit quantification / window months 4–10     LOW COST — second termination gate
Stage 3  Construct + model               months 8–18     MODERATE
Stage 4  Efficacy in symptomatic animals months 14–30    HIGH — PIVOTAL
Stage 5  Delivery / dose / safety        months 24–36     HIGH
Stage 6  Biomarkers / trial readiness    PARALLEL, from month 0
IND-enabling / toxicology               months 30–48     HIGHEST
```

**Stage 6 and registry enrolment start at month 0, in parallel, and are not optional.** Trial
readiness is on the critical path and is the component most often deferred until too late.

### What is being asked of the reader

| # | Request | Cost | Why it matters |
|---|---|---|---|
| 1 | **Confirm the variant molecularly** (Stage 1) | Low | Everything downstream is conditional on it. The 2018 report states the call was not independently confirmed |
| 2 | **Assess feasibility and the therapeutic window** (Stage 2) | Low | Terminates the programme cheaply if the window is inadequate |
| 3 | **Comment on the construct design** (Section 4.3) | None | In particular the M1-reinitiation hypothesis and the promoter choice |
| 4 | **Advise on model selection** (Section 6.3) | None | The C448Y model does not represent this variant class |
| 5 | **Advise on registry entry and trial geography** | None | The route by which this patient enters the research community |

### Sources and confidence

**Verified and relied upon:** Piermarini et al., *Mol Ther* 2025, PMID 41311060 · OMIM 604277
(*SPASTIN; SPAST*) — the splice/truncation vs. missense mechanistic distinction · GeneReviews
NBK1160 (SPG4 clinical characteristics) · Fink JK, *Arch Neurol* 2003 (mechanism via reduced
full-length spastin) · MELPIDA SPG50 NCT06069687 / NCT06692712 · Spieker et al., *Front Neurol*
2025, PMID 40917667 (negative sham-controlled tSCS in progressive MS) · Cipriano et al.,
*Ther Adv Neurol Disord* 2026, PMID 41537160 (HSP trial landscape, absence of core outcome set).

**Explicitly flagged as requiring verification before this document is relied upon.** These
originated in an automated literature search and were not confirmed against the primary source:

- HDAC6 inhibition / tubastatin A in SPG4 (*Cell Rep* 2026) — if confirmed, a pharmacological
  adjunct may be available and the drug–gene combination becomes a lower-risk option than any
  combination product
- AKV9 (formerly NU-9, AKAVA Therapeutics) preclinical data in the C448Y model; note AKV9 has a
  cleared IND in ALS, in which HSP is listed as an indication
- *SPAST* isoform lengths and the precise exon-to-domain mapping, which determine the payload
  calculation in Section 4.3
- The single-case report of SCS epidural stimulation in SPG4, cited here only as context

**Disclaimer.** This document is a concept proposal prepared with AI assistance by a
non-specialist. It is not a regulatory document, not a scientific protocol, and not clinical
advice. Its Stage 1 contents constitute **diagnostic laboratory recommendations that require
review and ordering by a qualified clinician**. Every claim above is intended to be evaluated,
challenged and corrected by qualified investigators — that is the purpose of circulating it.

---

*Contact: patient-initiated. Available for direct discussion of the underlying data.*
