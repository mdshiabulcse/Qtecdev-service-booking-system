<template>
  <v-container class="fill-height">
    <v-row justify="center" align="center">
      <v-col cols="12" sm="8" md="6" class="text-center">
        <h1 class="text-h1 mb-4">404</h1>
        <p class="text-h5 mb-4">Page Not Found</p>
        <v-btn
          color="primary"
          @click="$router.push('/')"
        >
          Go Home
        </v-btn>
      </v-col>
    </v-row>
  </v-container>
</template>
