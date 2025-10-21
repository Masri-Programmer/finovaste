<script setup lang="ts">
import { Button } from '@/components/ui/button'
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from '@/components/ui/tooltip'

defineProps<{
  tooltipText: string
  ariaLabel: string
}>()

const emit = defineEmits(['click'])

function handleClick() {
  emit('click')
}
</script>

<template>
  <TooltipProvider>
    <Tooltip :delay-duration="0">
      <TooltipTrigger as-child>
        <Button
          variant="ghost"
          size="icon"
          :aria-label="ariaLabel"
          @click="handleClick"
        >
          <slot />
        </Button>
      </TooltipTrigger>
      <TooltipContent>
        <p>{{ tooltipText }}</p>
      </TooltipContent>
    </Tooltip>
  </TooltipProvider>
</template>