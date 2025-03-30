<script setup lang="ts">
import WelcomeWidget from '@/Components/WelcomeWidget.vue';
import { formatMoney } from '@/utils';
import SectionFooterCard from './SectionFooterCard.vue';


const props = defineProps<{
    summaryType: string;
    paidCommissions: {
        totalInPeriod: number;
    };
    accounts: {
        cash_and_bank: {
            income: number;
            outcome: number;
        };
        credit_cards: {
            income: number;
            outcome: number;
        };
        other: {
            income: number;
            outcome: number;
        };
        total: {
            income: number;
            outcome: number;
        };
    };
    stats: {
        outstanding: number;
        outstanding_in_month: number;
        overdue: number;
    };
    isMobile: boolean;
}>();

</script>
<template>
    <WelcomeWidget :message="$t('Performance of the month')" class="shadow-sm" rounded size="default">
        <template #content>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
                <!-- Gross Earnings Card -->
                <div class="flex flex-col space-y-2">
                    <div class="flex items-baseline justify-between">
                        <h3 class="text-sm font-medium text-body-1">{{ $t('Gross earnings') }}</h3>
                        <span class="text-lg font-semibold text-body">{{ formatMoney(paidCommissions.totalInPeriod) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center space-x-1.5 text-success">
                            <IMdiArrowUpThick class="h-4 w-4" />
                            <span>{{ formatMoney(accounts.cash_and_bank.income) }}</span>
                            <span class="text-xs text-body-1 hidden md:inline">{{ $t("Inflow") }}</span>
                        </div>
                        <div class="flex items-center space-x-1.5 text-error/70">
                            <IMdiArrowDownThick class="h-4 w-4" />
                            <span>{{ formatMoney(accounts.cash_and_bank.outcome) }}</span>
                            <span class="text-xs text-body-1 hidden md:inline">{{ $t("Outflow") }}</span>
                        </div>
                    </div>
                </div>

                <!-- Pending Balance Card -->
                <div class="flex flex-col space-y-2 md:pl-4 md:border-l border-neutral/20">
                    <div class="flex items-baseline justify-between">
                        <h3 class="text-sm font-medium text-body-1">{{ $t('Pending balance') }}</h3>
                        <Link 
                            href="/property-reports?filters[owner]=&filters[property]=&filters[section]=invoices"
                            class="group text-right">
                            <span class="text-lg font-semibold text-body group-hover:text-primary transition-colors">
                                {{ formatMoney(stats.outstanding) }}
                            </span>
                            <span class="block text-sm text-body-1">
                                ({{ formatMoney(stats.outstanding_in_month) }})
                            </span>
                        </Link>
                    </div>
                    <div class="flex items-center justify-end">
                        <Link 
                            class="flex items-center space-x-1.5 text-sm text-error/70 hover:text-error transition-colors"
                            href="/property-reports?filters[owner]=&filters[property]=&filters[section]=invoices&filters[status]=overdue">
                            <IMdiFileDocumentAlertOutline class="h-4 w-4" />
                            <span>{{ formatMoney(stats.overdue) }}</span>
                            <span class="text-xs">{{ $t("Late") }}</span>
                        </Link>
                    </div>
                </div>
            </div>
        </template>
    </WelcomeWidget>
</template>