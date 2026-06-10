<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { useTrans } from '@/composables/useTrans';
import { store } from '@/routes/register';

const { __ } = useTrans();

defineOptions({
    layout: {
        title: 'Create an account',
        description: 'Enter your details below to create your account',
    },
});
</script>

<template>
    <Head :title="__('auth.register')" />

    <Form
        v-bind="store.form()"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="name">Nom Complet</Label>
                <Input
                    id="name"
                    type="text"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="name"
                    name="name"
                    placeholder="Nom complet"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="email">Votre Email</Label>
                <Input
                    id="email"
                    type="email"
                    required
                    :tabindex="2"
                    autocomplete="email"
                    name="email"
                    placeholder="Votre Email"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <Label for="phone">Votre numéro de téléphone</Label>
                <Input
                    id="phone"
                    type="tel"
                    :tabindex="3"
                    name="phone"
                    placeholder="+243xxxxxxxxxxx"
                />
                <InputError :message="errors.phone" />
            </div>

            <div class="grid gap-2">
                <Label for="photo">Votre photo de profil</Label>
                <Input
                    id="photo"
                    type="file"
                    accept="image/jpeg,image/png,image/webp"
                    :tabindex="4"
                    name="photo"
                />
                <InputError :message="errors.photo" />
            </div>

            <div class="grid gap-2">
                <Label for="password">Votre mot de passe</Label>
                <PasswordInput
                    id="password"
                    required
                    :tabindex="5"
                    autocomplete="new-password"
                    name="password"
                    placeholder="Votre mot de passe"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation">Confirmez votre mot de passe</Label>
                <PasswordInput
                    id="password_confirmation"
                    required
                    :tabindex="6"
                    autocomplete="new-password"
                    name="password_confirmation"
                    placeholder="Votre mot de passe"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <Button
                type="submit"
                class="mt-2 w-full"
                tabindex="7"
                :disabled="processing"
                data-test="register-user-button"
            >
                <Spinner v-if="processing" />
                {{ __('auth.create_account') }}
            </Button>
        </div>

        <div class="text-center text-sm text-muted-foreground">
            {{ __('auth.has_account') }}
            <TextLink
                :href="login()"
                class="underline underline-offset-4"
                :tabindex="8"
                >{{ __('auth.login') }}</TextLink
            >
        </div>
    </Form>
</template>
