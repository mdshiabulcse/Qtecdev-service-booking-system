<!-- src/components/Layout/Footer.vue -->
<template>
  <v-footer app color="surface" height="60" class="px-4 border-t">
    <v-container fluid class="pa-0">
      <v-row align="center" justify="space-between">
        <v-col cols="12" sm="6" class="text-center text-sm-start">
          <span class="text-caption grey--text">
            &copy; 2025 Simple School System. All rights reserved.
          </span>
        </v-col>
        <v-col cols="12" sm="6" class="text-center text-sm-end">
          <div class="d-flex justify-center justify-sm-end align-center">
            <v-btn icon variant="text" size="small" color="grey">
              <v-icon>mdi-help-circle</v-icon>
            </v-btn>
            <v-btn icon variant="text" size="small" color="grey">
              <v-icon>mdi-email</v-icon>
            </v-btn>
            <v-btn icon variant="text" size="small" color="grey">
              <v-icon>mdi-github</v-icon>
            </v-btn>
          </div>
        </v-col>
      </v-row>
    </v-container>
  </v-footer>
</template>

<style scoped>
.border-t {
  border-top: 1px solid rgba(0, 0, 0, 0.12);
}
</style>
