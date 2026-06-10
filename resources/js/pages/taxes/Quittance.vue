<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { useTrans } from '@/composables/useTrans';

const { __ } = useTrans();

const props = defineProps<{
    paiement: any;
    payable_label: string;
    payable_type_label: string;
    payable: any;
}>();

const methodLabel: Record<string, string> = {
    cash: __('paiements.method_cash_option'),
    bank: __('paiements.method_bank_option'),
    mobile_money: __('paiements.method_mobile'),
};

onMounted(() => {
    setTimeout(() => window.print(), 500);
});

const formatDate = (d: string) => new Date(d).toLocaleDateString('fr-FR', {
    day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
});

const formatAmount = (n: any, currency: string) =>
    Number(n).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ' + currency;
</script>

<template>
    <Head :title="__('paiements.quittance')" />

    <!-- Screen preview header (hidden when printing) -->
    <div class="no-print bg-slate-900 text-white px-6 py-3 flex items-center justify-between">
        <span class="text-sm font-bold">{{ __('paiements.quittance_preview') }} {{ paiement.reference }}</span>
        <div class="flex gap-3">
            <button @click="window.print()" class="px-4 py-1.5 bg-rdc-blue text-white rounded-lg text-sm font-bold hover:bg-rdc-blue/90">
                🖨 {{ __('paiements.quittance_print_btn') }}
            </button>
            <a href="/paiements" class="px-4 py-1.5 bg-slate-700 text-white rounded-lg text-sm font-bold hover:bg-slate-600">
                {{ __('paiements.quittance_back_btn') }}
            </a>
        </div>
    </div>

    <!-- Quittance Body (A4) -->
    <div class="quittance-page">

        <!-- Header de l'organisme -->
        <div class="quittance-header">
            <div class="org-left">
                <p class="org-country">{{ __('permis.card_country') }}</p>
                <p class="org-ministry">{{ __('certificats.card_ministry') }}</p>
                <p class="org-dept">{{ __('paiements.quittance_transport_dept') }}</p>
            </div>
            <div class="org-logo">
                <div class="logo-circle">DRC</div>
            </div>
            <div class="org-right">
                <p class="org-country">Tujikinge ERP</p>
                <p class="org-ministry">{{ __('app.tagline') }}</p>
                <p v-if="paiement.province" class="org-dept">{{ __('paiements.quittance_province_prefix') }} {{ paiement.province?.name }}</p>
                <p v-else class="org-dept">{{ __('common.national') }}</p>
            </div>
        </div>

        <div class="quittance-divider"></div>

        <!-- Titre -->
        <div class="quittance-title-block">
            <h1 class="quittance-title">{{ __('paiements.quittance_title') }}</h1>
            <p class="quittance-subtitle">{{ __('paiements.quittance_subtitle') }}</p>
        </div>

        <!-- Référence & Date -->
        <div class="meta-row">
            <div class="meta-box">
                <span class="meta-label">{{ __('paiements.quittance_ref_label') }}</span>
                <span class="meta-value ref">{{ paiement.reference }}</span>
            </div>
            <div class="meta-box">
                <span class="meta-label">{{ __('paiements.quittance_date_label') }}</span>
                <span class="meta-value">{{ formatDate(paiement.paid_at) }}</span>
            </div>
            <div class="meta-box">
                <span class="meta-label">{{ __('paiements.quittance_method_label') }}</span>
                <span class="meta-value">{{ methodLabel[paiement.payment_method] ?? paiement.payment_method }}</span>
            </div>
        </div>

        <!-- Corps principal -->
        <div class="quittance-body">

            <!-- Section Payeur -->
            <div class="section">
                <h2 class="section-title">{{ __('paiements.quittance_section_payeur') }}</h2>
                <table class="info-table">
                    <tbody>
                        <tr>
                            <td class="info-label">{{ __('paiements.quittance_payable_type') }}</td>
                            <td class="info-value">{{ payable_type_label }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">{{ __('paiements.quittance_payable_identity') }}</td>
                            <td class="info-value font-bold">{{ payable_label }}</td>
                        </tr>
                        <tr v-if="payable?.sector">
                            <td class="info-label">{{ __('paiements.quittance_payable_sector') }}</td>
                            <td class="info-value capitalize">{{ payable.sector }}</td>
                        </tr>
                        <tr v-if="payable?.province?.name">
                            <td class="info-label">{{ __('paiements.quittance_payable_province') }}</td>
                            <td class="info-value">{{ payable.province?.name }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Section Taxe -->
            <div class="section mt-4">
                <h2 class="section-title">{{ __('paiements.quittance_section_taxe') }}</h2>
                <table class="info-table">
                    <tbody>
                        <tr>
                            <td class="info-label">{{ __('paiements.quittance_taxe_label') }}</td>
                            <td class="info-value font-bold">{{ paiement.taxe?.label }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">{{ __('paiements.quittance_taxe_beneficiary') }}</td>
                            <td class="info-value">{{ paiement.taxe?.beneficiary }}</td>
                        </tr>
                        <tr v-if="paiement.taxe?.frequency">
                            <td class="info-label">{{ __('paiements.quittance_taxe_frequency') }}</td>
                            <td class="info-value">{{ paiement.taxe?.frequency }}</td>
                        </tr>
                        <tr v-if="paiement.notes">
                            <td class="info-label">{{ __('paiements.quittance_taxe_notes') }}</td>
                            <td class="info-value">{{ paiement.notes }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Montant total -->
            <div class="amount-block">
                <span class="amount-label">{{ __('paiements.quittance_amount_label') }}</span>
                <span class="amount-value">{{ formatAmount(paiement.amount, paiement.currency) }}</span>
            </div>

        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-box">
                <p class="sig-title">{{ __('paiements.quittance_sig_collector') }}</p>
                <div class="sig-line"></div>
                <p class="sig-name">{{ paiement.user?.name ?? '____________________' }}</p>
            </div>
            <div class="signature-box text-center">
                <p class="sig-title">{{ __('paiements.quittance_sig_official') }}</p>
                <div class="sig-stamp"></div>
            </div>
            <div class="signature-box text-right">
                <p class="sig-title">{{ __('paiements.quittance_sig_payeur') }}</p>
                <div class="sig-line"></div>
                <p class="sig-name">____________________</p>
            </div>
        </div>

        <!-- Pied de page -->
        <div class="quittance-footer">
            <p>{{ __('paiements.quittance_footer') }}</p>
            <p class="mt-1">{{ __('paiements.quittance_footer_erp') }} • {{ new Date().getFullYear() }}</p>
        </div>

    </div>
</template>

<style scoped>
* { box-sizing: border-box; }

.quittance-page {
    width: 210mm;
    min-height: 297mm;
    margin: 0 auto;
    padding: 14mm 16mm;
    background: #fff;
    font-family: 'Times New Roman', Times, serif;
    color: #1a1a1a;
    font-size: 11pt;
}

/* Header organisme */
.quittance-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 6mm;
}
.org-left, .org-right { flex: 1; }
.org-right { text-align: right; }
.org-country { font-size: 9pt; font-weight: 900; text-transform: uppercase; letter-spacing: 0.05em; }
.org-ministry { font-size: 8.5pt; font-weight: 700; margin-top: 2px; }
.org-dept { font-size: 8pt; color: #555; margin-top: 1px; }
.org-logo { display: flex; justify-content: center; align-items: center; }
.logo-circle {
    width: 18mm; height: 18mm;
    border-radius: 50%;
    border: 2px solid #1a3a6b;
    display: flex; align-items: center; justify-content: center;
    font-size: 10pt; font-weight: 900; color: #1a3a6b;
    letter-spacing: 0.1em;
}

.quittance-divider {
    height: 3px;
    background: linear-gradient(to right, #1a3a6b, #c00, #1a3a6b);
    margin-bottom: 5mm;
}

/* Titre */
.quittance-title-block { text-align: center; margin-bottom: 5mm; }
.quittance-title {
    font-size: 16pt; font-weight: 900; text-transform: uppercase;
    letter-spacing: 0.08em; color: #1a3a6b;
    border-bottom: 1.5pt solid #c00;
    display: inline-block; padding-bottom: 2mm;
}
.quittance-subtitle { font-size: 9pt; color: #666; margin-top: 2mm; }

/* Meta row */
.meta-row {
    display: flex; gap: 4mm; margin-bottom: 5mm;
}
.meta-box {
    flex: 1; border: 1pt solid #1a3a6b; border-radius: 3px;
    padding: 3mm; display: flex; flex-direction: column;
}
.meta-label { font-size: 7.5pt; text-transform: uppercase; color: #555; font-weight: 700; }
.meta-value { font-size: 10pt; font-weight: 700; margin-top: 1mm; }
.meta-value.ref { color: #1a3a6b; font-size: 11pt; font-family: monospace; font-weight: 900; }

/* Sections */
.section { margin-bottom: 4mm; }
.section-title {
    font-size: 9pt; font-weight: 900; text-transform: uppercase;
    color: #1a3a6b; border-bottom: 0.5pt solid #1a3a6b;
    padding-bottom: 1.5mm; margin-bottom: 2mm; letter-spacing: 0.05em;
}
.info-table { width: 100%; border-collapse: collapse; }
.info-table tr td { padding: 1.5mm 2mm; vertical-align: top; font-size: 10pt; }
.info-label { color: #444; width: 40%; border-right: 0.5pt solid #ddd; padding-right: 3mm; }
.info-value { padding-left: 3mm; }
.info-table tr:nth-child(even) td { background: #f7f9fc; }

/* Montant */
.amount-block {
    margin-top: 5mm;
    border: 2pt solid #1a3a6b;
    border-radius: 4px;
    padding: 4mm 6mm;
    display: flex; align-items: center; justify-content: space-between;
    background: #f0f4fb;
}
.amount-label { font-size: 11pt; font-weight: 900; text-transform: uppercase; color: #1a3a6b; letter-spacing: 0.08em; }
.amount-value { font-size: 18pt; font-weight: 900; color: #c00; }

/* Signatures */
.signatures {
    display: flex; justify-content: space-between;
    margin-top: 12mm; gap: 10mm;
}
.signature-box { flex: 1; }
.sig-title { font-size: 8.5pt; font-weight: 700; text-align: center; margin-bottom: 10mm; }
.sig-line { border-bottom: 0.5pt solid #333; width: 80%; margin: 0 auto; }
.sig-name { font-size: 8pt; text-align: center; margin-top: 1.5mm; color: #555; }
.sig-stamp {
    width: 22mm; height: 22mm;
    border: 1pt dashed #999; border-radius: 50%;
    margin: 0 auto;
}

/* Footer */
.quittance-footer {
    margin-top: 8mm;
    border-top: 0.5pt solid #ccc;
    padding-top: 3mm;
    font-size: 7.5pt;
    color: #888;
    text-align: center;
}

/* Print specific */
@media print {
    .no-print { display: none !important; }
    .quittance-page {
        margin: 0;
        padding: 10mm 14mm;
        width: 100%;
    }
    @page {
        size: A4;
        margin: 0;
    }
}

/* Screen only */
@media screen {
    .no-print { display: flex; }
    body { background: #e5e7eb; }
    .quittance-page {
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        margin: 6mm auto 20mm;
    }
}
</style>
