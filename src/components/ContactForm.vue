<template>
    <div>
        <!-- The <Form> component from VeeValidate handles state and validation -->
        <Form @submit="handleSubmit" :validation-schema="submissionSchema" v-slot="{ isSubmitting }">
            <!-- Full Name Field -->
            <div>
                <label for="fullName">Full Name</label>
                <Field name="fullName" type="text" id="fullName" />
                <ErrorMessage name="fullName" class="error-message" />
            </div>

            <!-- Email Address Field -->
            <div>
                <label for="email">Email Address</label>
                <Field name="email" type="email" id="email" />
                <ErrorMessage name="email" class="error-message" />
            </div>

            <!-- Phone Number Field with Input Masking -->
            <div>
                <label for="phone">Phone Number</label>
                <!-- 
          The v-maska directive automatically formats the input.
          - The data-maska attribute defines the pattern.
          - The underlying value sent for validation will be the raw digits (e.g., "1234567890").
        -->
                <Field name="phone" type="tel" id="phone" v-maska data-maska="(###) ###-####" />
                <ErrorMessage name="phone" class="error-message" />
            </div>

            <!-- Subject Field -->
            <div>
                <label for="subject">Subject</label>
                <Field name="subject" type="text" id="subject" />
                <ErrorMessage name="subject" class="error-message" />
            </div>

            <!-- Submit Button -->
            <button type="submit" :disabled="isSubmitting">
                {{ isSubmitting ? 'Submitting...' : 'Submit' }}
            </button>
        </Form>

        <!-- Display success or error messages after submission -->
        <div v-if="successMessage">
            <p>{{ successMessage }}</p>
        </div>
        <div v-if="error">
            <p>Error: {{ error }}</p>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
// Import VeeValidate components and Zod for validation
import { Form, Field, ErrorMessage } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';
import * as z from 'zod';
// Import the Maska directive for input formatting
// FIX: The import path for the Vue directive is 'maska/vue'
import { vMaska } from 'maska/vue';

/*
  INSTALL DEPENDENCIES:
  npm install vee-validate @vee-validate/zod zod maska
*/

// Define the validation schema using Zod
const submissionSchema = toTypedSchema(
    z.object({
        fullName: z.string().min(1, 'Full name is required'),
        email: z.string().min(1, 'Email is required').email('Please enter a valid email address'),

        // --- FIX: UPDATED PHONE VALIDATION ---
        // The validation now uses a regex to match the Maska format (e.g., "(123) 456-7890").
        // The field is still optional, but if a value is present, it must be a complete phone number.
        phone: z.string()
            .regex(/^\(\d{3}\) \d{3}-\d{4}$/, 'Phone number is incomplete or invalid')
            .optional()
            .or(z.literal('')),

        subject: z.string().min(1, 'Subject is required'),
    })
);

// Reactive variables to track the form's submission state
const error = ref(null);
const successMessage = ref('');

/**
 * This function is called when the form is submitted and passes validation.
 * @param {object} values - The validated form values, provided by VeeValidate.
 * @param {object} actions - VeeValidate actions, like resetForm.
 */
async function handleSubmit(values, { resetForm }) {
    // Reset state before submission
    error.value = null;
    successMessage.value = '';

    try {
        // Send the validated form data to our custom WordPress REST API endpoint
        const response = await fetch('/wp-json/indago/v1/submissions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            // Convert the validated values object to a JSON string
            body: JSON.stringify(values),
        });

        // Check if the request was successful
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'An unknown error occurred.');
        }

        // If successful, show a success message and reset the form fields
        const result = await response.json();
        successMessage.value = result.message;
        resetForm(); // Use VeeValidate's action to clear the form

    } catch (err) {
        // If an error occurs, store the error message to display it
        error.value = err.message;
    }
}
</script>

<style scoped>
/* A simple style for error messages */
.error-message {
    color: red;
    font-size: 0.875em;
}
</style>