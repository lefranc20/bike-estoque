<script setup>
import { computed } from 'vue'

const props = defineProps({
  dados: {
    type: Array,
    default: () => []
  },
  granularidade: {
    type: String,
    default: 'dia'
  }
})

const MESES_ABREVIADOS = ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez']

const maxValor = computed(() => {
  const valores = props.dados.flatMap((dia) => [dia.entradas, dia.saidas])
  return Math.max(1, ...valores)
})

const semMovimentacoes = computed(() => {
  return props.dados.every((dia) => dia.entradas === 0 && dia.saidas === 0)
})

const muitasBarras = computed(() => props.dados.length > 10)
const barrasDensas = computed(() => props.dados.length > 20)

function altura(valor) {
  return (valor / maxValor.value) * 100
}

function formatarData(data) {
  if (props.granularidade === 'ano') {
    return data
  }

  if (props.granularidade === 'mes') {
    const [ano, mes] = data.split('-')
    return `${MESES_ABREVIADOS[Number(mes) - 1]}/${ano.slice(2)}`
  }

  const [, mes, dia] = data.split('-')
  return `${dia}/${mes}`
}
</script>

<template>
  <div class="movement-chart">
    <div class="movement-chart-legend">
      <span class="legend-item"><span class="legend-dot legend-entrada"></span> Entradas</span>
      <span class="legend-item"><span class="legend-dot legend-saida"></span> Saídas</span>
    </div>

    <div class="movement-chart-bars" :class="{ 'many-bars': muitasBarras, 'dense-bars': barrasDensas }">
      <div v-for="dia in dados" :key="dia.data" class="movement-chart-day">
        <div class="movement-chart-bar-group">
          <div
            class="movement-chart-bar entrada"
            :style="{ height: altura(dia.entradas) + '%' }"
            :title="`Entradas em ${formatarData(dia.data)}: ${dia.entradas}`"
          ></div>
          <div
            class="movement-chart-bar saida"
            :style="{ height: altura(dia.saidas) + '%' }"
            :title="`Saídas em ${formatarData(dia.data)}: ${dia.saidas}`"
          ></div>
        </div>
        <span class="movement-chart-label">{{ formatarData(dia.data) }}</span>
      </div>
    </div>

    <p v-if="semMovimentacoes" class="status-summary movement-chart-empty">
      Nenhuma movimentação no período selecionado.
    </p>
  </div>
</template>

<style scoped>
.movement-chart-legend {
  display: flex;
  gap: 1.25rem;
  margin-bottom: 1rem;
  font-size: 0.8rem;
  color: var(--cor-texto-medio);
}

.legend-item {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
}

.legend-dot {
  width: 0.6rem;
  height: 0.6rem;
  border-radius: 50%;
}

.legend-entrada {
  background: var(--cor-sucesso-texto);
}

.legend-saida {
  background: var(--cor-erro-texto);
}

.movement-chart-bars {
  display: flex;
  align-items: flex-end;
  gap: 0.5rem;
  height: 140px;
}

.movement-chart-day {
  flex: 1 1 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  height: 100%;
  min-width: 0;
}

.movement-chart-bar-group {
  flex: 1;
  width: 100%;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  gap: 3px;
}

.movement-chart-bar {
  width: 40%;
  min-height: 2px;
  border-radius: 4px 4px 0 0;
  transition: height 0.3s ease, background-color 0.25s ease;
}

.movement-chart-bar.entrada {
  background: var(--cor-sucesso-texto);
}

.movement-chart-bar.saida {
  background: var(--cor-erro-texto);
}

.movement-chart-label {
  margin-top: 0.5rem;
  font-size: 0.6rem;
  color: var(--cor-texto-fraco);
  white-space: nowrap;
}

.movement-chart-empty {
  text-align: center;
  margin-top: 1rem;
}

@media (max-width: 620px) {
  .movement-chart-bars.many-bars .movement-chart-day:nth-child(odd) .movement-chart-label {
    visibility: hidden;
  }

  .movement-chart-bars.dense-bars {
    justify-content: flex-start;
    overflow-x: auto;
    padding-bottom: 4px;
  }

  .movement-chart-bars.dense-bars .movement-chart-day {
    flex: 0 0 28px;
  }

  .movement-chart-bars.dense-bars .movement-chart-day:nth-child(odd) .movement-chart-label {
    visibility: visible;
  }
}
</style>